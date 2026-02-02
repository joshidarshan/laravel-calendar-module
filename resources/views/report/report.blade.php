@extends('layouts.app')

@section('topbar-title', 'Employee Report')

@section('topbar-buttons')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
.filter-group, .view-group {
    padding-left: 20px;
    padding-top: 14px;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div class="btn-group filter-group">
        <button class="btn btn-primary filter-btn active" data-filter="day">Day</button>
        <button class="btn btn-outline-primary filter-btn" data-filter="week">Week</button>
        <button class="btn btn-outline-primary filter-btn" data-filter="month">Month</button>
        <button class="btn btn-outline-secondary filter-btn" data-filter="all">All</button>
    </div>

    <div class="btn-group view-group">
        <a href="#" class="btn btn-outline-dark" id="btnTable">
            <i class="bi bi-table"></i>
        </a>
        <a href="#" class="btn btn-outline-dark" id="btnChart">
            <i class="bi bi-bar-chart"></i>
        </a>
    </div>

</div>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <button id="prevBtn" class="btn btn-outline-secondary">⬅ Prev</button>
    <div class="fw-bold fs-5" id="reportLabel"></div>
    <button id="nextBtn" class="btn btn-outline-secondary">Next ➡</button>
</div>

<div id="tableView">
    <table id="reportTable" class="table table-bordered text-center w-100">
        <thead>
            <tr>
                <th class="text-start">Employee</th>
                <th>Total</th>
                <th>Score</th>
                <th>Pending</th>
                <th>Progress</th>
                <th>Completed</th>
                <th>Overdue</th>
            </tr>
        </thead>
    </table>
</div>

<div id="chartView" class="d-none">
    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card p-3"><canvas id="statusChart"></canvas></div></div>
        <div class="col-md-4"><div class="card p-3"><canvas id="completionChart"></canvas></div></div>
        <div class="col-md-4"><div class="card p-3"><canvas id="timingChart"></canvas></div></div>
    </div>

    <div class="row g-3">
        <div class="col-md-6"><div class="card p-3"><h6>Employee Wise</h6><canvas id="employeeChart"></canvas></div></div>
        <div class="col-md-6"><div class="card p-3"><h6>Category Wise</h6><canvas id="categoryChart"></canvas></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let filter = 'day';
let offset = 0;

const btnTable = document.getElementById('btnTable');
const btnChart = document.getElementById('btnChart');
const tableView = document.getElementById('tableView');
const chartView = document.getElementById('chartView');
const labelEl = document.getElementById('reportLabel');
const table = document.getElementById('reportTable');

let statusChart, completionChart, timingChart, employeeChart, categoryChart;

btnTable.addEventListener('click', e => {
    e.preventDefault();
    tableView.classList.remove('d-none');
    chartView.classList.add('d-none');
});

btnChart.addEventListener('click', e => {
    e.preventDefault();
    tableView.classList.add('d-none');
    chartView.classList.remove('d-none');
    renderCharts(window.currentData || []);
});

function loadReport() {
    fetch(`/api/employee-report?filter=${filter}&offset=${offset}`, {
        credentials: 'same-origin',
        headers: {'Accept': 'application/json'}
    })
    .then(res => res.status === 401 ? window.location.href='/login' : res.json())
    .then(res => {
        labelEl.innerText = res.label;
        renderTable(res.data);

        // ✅ Update charts if chartView is visible
        if (!chartView.classList.contains('d-none')) {
            renderCharts(res.data);
        }
    })
    .catch(err => console.error(err));
}


function renderTable(data) {
    window.currentData = data; // store for charts
    let tbody = `<tbody>`;

    if(!data.length){
        tbody += `<tr><td colspan="7" class="text-muted py-3">No data found</td></tr>`;
    } else {
        data.forEach(r => {
            tbody += `<tr>
                <td class="text-start">${r.employee}</td>
                <td>${r.total}</td>
                <td>${r.score}</td>
                <td>${r.pending}</td>
                <td>${r.progress}</td>
                <td>${r.completed}</td>
                <td>${r.overdue}</td>
            </tr>`;
        });
    }

    tbody += `</tbody>`;
    table.querySelector('tbody')?.remove();
    table.insertAdjacentHTML('beforeend', tbody);
}

function shortName(name) {
    if (!name) return '';
    const parts = name.trim().split(' ');
    if (parts.length === 1) return parts[0];
    return parts[0] + ' ' + parts[1].charAt(0) + '.';
}

// ---------------- Filter buttons ----------------
document.querySelectorAll('.filter-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.querySelectorAll('.filter-btn').forEach(b=>{
            b.classList.remove('btn-primary','active');
            b.classList.add('btn-outline-primary');
        });
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary','active');
        filter = btn.dataset.filter;
        offset = 0;
        loadReport();
    });
});

// ---------------- Prev / Next ----------------
document.getElementById('prevBtn').onclick = () => { offset--; loadReport(); };
document.getElementById('nextBtn').onclick = () => { offset++; loadReport(); };

// ---------------- Render Charts ----------------
function renderCharts(data){
    const totalPending = data.reduce((sum,d)=>sum+d.pending,0);
    const totalProgress = data.reduce((sum,d)=>sum+d.progress,0);
    const totalCompleted = data.reduce((sum,d)=>sum+d.completed,0);
    const totalOverdue = data.reduce((sum,d)=>sum+d.overdue,0);

    [statusChart, completionChart, timingChart, employeeChart, categoryChart].forEach(c=>{if(c)c.destroy();});

    // Status
    statusChart = new Chart(document.getElementById('statusChart'),{
        type:'doughnut',
        data:{labels:['Pending','Progress','Completed','Overdue'], datasets:[{data:[totalPending,totalProgress,totalCompleted,totalOverdue], backgroundColor:['#f59e0b','#3b82f6','#10b981','#ef4444']}]}
    });

    // Completion
    completionChart = new Chart(document.getElementById('completionChart'),{
        type:'doughnut',
        data:{labels:['Completed','Remaining'], datasets:[{data:[totalCompleted, data.reduce((sum,d)=>sum+d.total,0)-totalCompleted], backgroundColor:['#10b981','#e5e7eb']}]}
    });

    // Timing
    timingChart = new Chart(document.getElementById('timingChart'),{
        type:'doughnut',
        data:{labels:['On-Time','Overdue'], datasets:[{data:[data.reduce((sum,d)=>sum+d.total,0)-totalOverdue,totalOverdue], backgroundColor:['#3b82f6','#ef4444']}]}
    });

    // Employee wise
// ---- Top 5 employees (by total tasks) ----
const topEmployees = [...data]
    .sort((a, b) => (b.total || 0) - (a.total || 0))
    .slice(0, 5);

// Employee wise
employeeChart = new Chart(document.getElementById('employeeChart'), {
    type: 'bar',
    data: {
        labels: topEmployees.map(d => shortName(d.employee)),
       datasets: [
            {
                label: 'Completed',
                data: topEmployees.map(d => d.completed || 0),
                backgroundColor: '#10b981' // green
            },
            {
                label: 'Pending',
                data: topEmployees.map(d => d.pending || 0),
                backgroundColor: '#f59e0b' // yellow / orange
            },
            {
                label: 'Progress',
                data: topEmployees.map(d => d.progress || 0),
                backgroundColor: '#3b82f6' // blue
            },
            {
                label: 'Overdue',
                data: topEmployees.map(d => d.overdue || 0),
                backgroundColor: '#ef4444' // red
            }
        ]
    },
   options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,        // ✅ 0,1,2,3
                    precision: 0        // ✅ remove decimal
                }
            }
        }
    }
});


    // Category wise
    categoryChart = new Chart(document.getElementById('categoryChart'),{
        type:'bar',
        data:{
            labels:['Pending','Progress','Completed','Overdue'],
            datasets:[{label:'Tasks', data:[totalPending,totalProgress,totalCompleted,totalOverdue], backgroundColor:['#f59e0b','#3b82f6','#10b981','#ef4444'] }]
        },
        options:{responsive:true, plugins:{legend:{display:false}}}
    });
}

// ---------------- Initial load ----------------
loadReport();
</script>
@endpush
