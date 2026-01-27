@extends('layouts.app')

@push('styles')
<style>
    body {
        background: #f3f4f6;
    }

    /* ---------- Filter Bar ---------- */
    .filter-bar {
        background: #ffffff;
        border-radius: 14px;
        padding: 10px 14px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .filter-left,
    .nav-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-left .btn,
    .nav-right .btn {
        padding: 6px 14px;
        font-size: 13px;
        border-radius: 8px;
    }

    /* ---------- Dashboard Card ---------- */
    .dashboard-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 220px;
    }

    .dashboard-title {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        text-align: center;
        margin-bottom: 6px;
    }

    .dashboard-card canvas {
        height: 320px !important;
    }
</style>
@endpush

@section('content')

{{-- ================= FILTER BAR ================= --}}
<div class="filter-bar">

    {{-- LEFT : FILTERS --}}
    <div class="filter-left">
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#dayModal">Day</button>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#weekModal">Week</button>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#monthModal">Month</button>
        <a href="{{ route('report.charts') }}" class="btn btn-outline-secondary btn-sm">All</a>
    </div>

    {{-- RIGHT : CHART / TABLE --}}
    <div class="nav-right">
        <a href="{{ route('report.charts') }}" class="btn btn-primary btn-sm">Charts</a>
        <a href="{{ route('report.table') }}" class="btn btn-outline-primary btn-sm">Table</a>
    </div>
</div>

{{-- ================= DAY MODAL ================= --}}
<div class="modal fade" id="dayModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h6 class="modal-title">Select Day</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <input type="date" class="form-control mx-auto" style="max-width:220px"
                       onchange="filterDay(this.value)">
            </div>
        </div>
    </div>
</div>

{{-- ================= WEEK MODAL ================= --}}
<div class="modal fade" id="weekModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h6 class="modal-title">Select Week</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <input type="week" class="form-control mx-auto" style="max-width:220px"
                       onchange="filterWeek(this.value)">
            </div>
        </div>
    </div>
</div>

{{-- ================= MONTH MODAL ================= --}}
<div class="modal fade" id="monthModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h6 class="modal-title">Select Month</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <input type="month" class="form-control mx-auto" style="max-width:220px"
                       onchange="filterMonth(this.value)">
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    {{-- ================= TOP CHARTS ================= --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-title">Overdue / Pending / In Progress</div>
                <canvas id="chart1"></canvas>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-title">Completed vs Not Completed</div>
                <canvas id="chart2"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="dashboard-title">In-Time vs Delayed</div>
                <canvas id="chart3"></canvas>
            </div>
        </div>
    </div>

    {{-- ================= ANALYTICS ================= --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-title">Employee Wise Task Status</div>
                <canvas id="employeeBarChart"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-title">Category Wise Task Status</div>
                <canvas id="categoryBarChart"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* ---------- FILTER FUNCTIONS ---------- */
    function filterDay(date) {
        if (!date) return;
        bootstrap.Modal.getInstance(dayModal).hide();
        window.location.href = `?filter=day&date=${date}`;
    }

    function filterWeek(week) {
        if (!week) return;
        bootstrap.Modal.getInstance(weekModal).hide();
        window.location.href = `?filter=week&week=${week}`;
    }

    function filterMonth(month) {
        if (!month) return;
        bootstrap.Modal.getInstance(monthModal).hide();
        window.location.href = `?filter=month&month=${month}`;
    }

    /* ---------- DATA ---------- */
    const summary = @json($summary);
    const employeeBar = @json($employeeBar);
    const categoryBar = @json($categoryBar);

    /* ---------- CHARTS ---------- */
    new Chart(chart1, {
        type: 'doughnut',
        data: {
            labels: ['Overdue', 'Pending', 'In Progress'],
            datasets: [{
                data: [summary.overdue, summary.pending, summary.in_progress],
                backgroundColor: ['#f87171', '#facc15', '#3b82f6']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(chart2, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Not Completed'],
            datasets: [{
                data: [summary.completed, summary.not_completed],
                backgroundColor: ['#10b981', '#f87171']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(chart3, {
        type: 'doughnut',
        data: {
            labels: ['In-Time', 'Delayed'],
            datasets: [{
                data: [summary.in_time, summary.delayed],
                backgroundColor: ['#3b82f6', '#f87171']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(employeeBarChart, {
        type: 'bar',
        data: {
            labels: employeeBar.map(e => e.name),
            datasets: [
                { label: 'Completed', data: employeeBar.map(e => e.completed), backgroundColor: '#10b981' },
                { label: 'Pending', data: employeeBar.map(e => e.pending), backgroundColor: '#facc15' },
                { label: 'Overdue', data: employeeBar.map(e => e.overdue), backgroundColor: '#f87171' },
                { label: 'In Progress', data: employeeBar.map(e => e.in_progress), backgroundColor: '#3b82f6' }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(categoryBarChart, {
        type: 'bar',
        data: {
            labels: Object.keys(categoryBar),
            datasets: [{
                label: 'Tasks',
                data: Object.values(categoryBar),
                backgroundColor: ['#10b981', '#facc15', '#f87171', '#3b82f6']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
