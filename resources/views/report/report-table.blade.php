@extends('layouts.app')

@section('topbar-title', 'Employee Report')

@section('topbar-buttons')
    @include('report._filterbar')
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
            <th>Overdue</th>
            <th>Pending</th>
            <th>Progress</th>
            <th>Completed</th>
            <th>Delayed</th>
        </tr>
        </thead>
    </table>
</div>

<div id="chartView" class="d-none">
    <div class="card p-4">
        <canvas id="reportChart" height="120"></canvas>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ================= STATE ================= */
let filter = 'all';   // ✅ DEFAULT ALL
let param  = null;
let view   = 'table';

/* ================= TABLE ================= */
const table = $('#reportTable').DataTable({
    serverSide: true,
    processing: true,
    ajax: {
        url: "{{ route('report.data') }}",
        data: d => {
            d.filter = filter;
            if (param) {
                if (filter === 'day')   d.date  = param;
                if (filter === 'week')  d.week  = param;
                if (filter === 'month') d.month = param;
            }
        }
    },
    columns: [
        {data:'name',className:'text-start'},
        {data:'total'},
        {data:'score'},
        {data:'overdue'},
        {data:'pending'},
        {data:'in_progress'},
        {data:'completed'},
        {data:'delayed'}
    ]
});

table.on('xhr', () => {
    const j = table.ajax.json();
    $('#reportLabel').text(j.label ?? '');
    updateNav(j.prev, j.next);
});

/* ================= CHART ================= */
const chart = new Chart(document.getElementById('reportChart'), {
    type:'bar',
    data:{
        labels:['Completed','Pending','Overdue','In Progress','Delayed'],
        datasets:[{
            data:[],
            backgroundColor:['#28a745','#ffc107','#dc3545','#0dcaf0','#6c757d']
        }]
    }
});

function loadChart(){
    $.get("{{ route('report.chart.data') }}", {
        filter: filter,
        date: filter==='day' ? param : null,
        week: filter==='week' ? param : null,
        month: filter==='month' ? param : null
    }, r => {
        chart.data.datasets[0].data = [
            r.summary.completed,
            r.summary.pending,
            r.summary.overdue,
            r.summary.in_progress,
            r.summary.delayed
        ];
        chart.update();
        $('#reportLabel').text(r.label);
        updateNav(r.prev, r.next);
    });
}


/* ================= NAV ================= */
function updateNav(prev,next){
    $('#prevBtn').data('value',prev).prop('disabled',!prev||filter==='all');
    $('#nextBtn').data('value',next).prop('disabled',!next||filter==='all');
}

$('#prevBtn').click(()=>{
    param=$('#prevBtn').data('value');
    view==='table'?table.ajax.reload():loadChart();
});

$('#nextBtn').click(()=>{
    param=$('#nextBtn').data('value');
    view==='table'?table.ajax.reload():loadChart();
});

/* ================= FILTER ================= */
$('.filter-btn').click(function(){
    $('.filter-btn').removeClass('btn-primary active')
        .addClass('btn-outline-primary');

    $(this).addClass('btn-primary active')
        .removeClass('btn-outline-primary');

    filter = $(this).data('filter');
    param  = null;

    view === 'table'
        ? table.ajax.reload()
        : loadChart();
});


/* ================= VIEW TOGGLE ================= */
$('.bi-table').closest('a').click(e=>{
    e.preventDefault();
    view='table';
    $('#chartView').addClass('d-none');
    $('#tableView').removeClass('d-none');
    table.ajax.reload();
});

$('.bi-bar-chart').closest('a').click(e=>{
    e.preventDefault();
    view='chart';
    $('#tableView').addClass('d-none');
    $('#chartView').removeClass('d-none');
    loadChart();
});

/* ================= INIT ================= */
table.ajax.reload();
</script>
@endpush
