@extends('layouts.app')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
        font-family: 'Inter', sans-serif;
    }

    /* FILTER + TAB BAR */
  .filter-bar {
    background: #ffffff;
    border-radius: 14px;
    padding: 10px 14px;
    box-shadow: 0 6px 16px rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.filter-left,
.filter-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-left .btn,
.filter-right .btn {
    padding: 6px 14px;
    font-size: 13px;
}

.filter-divider {
    width: 1px;
    height: 28px;
    background: #e5e7eb;
    margin: 0 10px;
}


    /* SUMMARY CARDS */
    .summary-card {
        background: linear-gradient(135deg, #ffffff, #f9fafb);
        border-radius: 16px;
        padding: 18px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,.06);
        transition: transform .2s ease;
    }

    .summary-card:hover {
        transform: translateY(-4px);
    }

    .summary-title {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 800;
    }

    /* REPORT CARD */
    .report-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,.08);
    }

    .report-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 14px;
    }

    /* DONUT */
    .progress-donut {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        margin-right: 10px;
    }

    .employee-name {
        display: flex;
        align-items: center;
        font-weight: 600;
    }

    /* TABLE */
    table {
        border-radius: 14px;
        overflow: hidden;
    }

    thead {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        color: #fff;
    }

    tbody tr:hover {
        background: #f1f5f9;
    }

    .btn-sm {
        padding: 6px 14px;
        border-radius: 999px;
    }

</style>
@endpush

@section('content')

{{-- FILTER + TABS --}}
<div class="filter-bar">

    <!-- LEFT : FILTERS -->
    <div class="filter-left">
        <button class="btn btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#dayPickerModal">
            Day
        </button>

       <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#weekPickerModal">
    Week
</button>


        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#monthPickerModal">
    Month
</button>


        <a href="{{ route('report.table', ['filter' => 'all']) }}"
           class="btn btn-outline-secondary">
            All
        </a>
    </div>

    <!-- RIGHT : CHART / TABLE -->
    <div class="filter-right">
        <a href="{{ route('report.charts') }}"
           class="btn btn-outline-primary {{ request()->routeIs('report.charts') ? 'active' : '' }}">
            Charts
        </a>

        <a href="{{ route('report.table') }}"
           class="btn btn-primary {{ request()->routeIs('report.table') ? 'active' : '' }}">
            Table
        </a>
    </div>

</div>


{{-- DAY MODAL --}}
<div class="modal fade" id="dayPickerModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h6 class="modal-title">Select Date</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <input type="date" class="form-control mx-auto" style="max-width:220px"
                       onchange="submitDay(this.value)">
            </div>
        </div>
    </div>
</div>
<!-- WEEK PICKER MODAL -->
<div class="modal fade" id="weekPickerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Week</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <input type="week"
                       class="form-control mx-auto"
                       style="max-width:240px"
                       onchange="submitWeek(this.value)">
            </div>
        </div>
    </div>
</div>
<!-- MONTH PICKER MODAL -->
<div class="modal fade" id="monthPickerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Month</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <input type="month"
                       class="form-control mx-auto"
                       style="max-width:240px"
                       onchange="submitMonth(this.value)">
            </div>
        </div>
    </div>
</div>

{{-- <input type="month" id="monthPicker" class="d-none" onchange="submitMonth(this.value)"> --}}

@php
    $totalTasks = collect($employeeWise)->sum(fn($e) =>
        $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress']
    );
    $completed = collect($employeeWise)->sum('completed');
    $pending = collect($employeeWise)->sum('pending');
    $overdue = collect($employeeWise)->sum('overdue');
    $inProgress = collect($employeeWise)->sum('in_progress');
    $delayed = collect($employeeWise)->sum('delayed');
    $score = $totalTasks ? round(($completed / $totalTasks) * 100) : 0;
@endphp

{{-- SUMMARY --}}
<div class="row g-4 mb-4">
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Total</div><div class="summary-value">{{ $totalTasks }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Score</div><div class="summary-value text-primary">{{ $score }}%</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Overdue</div><div class="summary-value text-danger">{{ $overdue }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Pending</div><div class="summary-value text-warning">{{ $pending }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">In Progress</div><div class="summary-value">{{ $inProgress }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">In Time</div><div class="summary-value text-success">{{ $completed }}</div></div></div>
</div>

{{-- TABLE --}}
<div class="report-card">
    <div class="report-title">Employee Performance</div>

    <table class="table align-middle text-center">
        <thead>
            <tr>
                <th class="text-start">Employee</th>
                <th>Total</th>
                <th>Score</th>
                <th>Overdue</th>
                <th>Pending</th>
                <th>Progress</th>
                <th>In Time</th>
                <th>Delayed</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($employeeWise as $emp)
            @php
                $total = $emp['completed'] + $emp['pending'] + $emp['overdue'] + $emp['in_progress'];
                $score = $total ? round(($emp['completed'] / $total) * 100) : 0;
                $color = $score >= 80 ? '#22c55e' : ($score >= 50 ? '#f59e0b' : '#ef4444');
                $gradient = "conic-gradient($color 0% {$score}%, #e5e7eb {$score}% 100%)";
            @endphp
            <tr>
                <td class="employee-name text-start">
                    <div class="progress-donut" style="background: {{ $gradient }}">{{ $score }}%</div>
                    {{ $emp['name'] }}
                </td>
                <td>{{ $total }}</td>
                <td class="fw-bold">{{ $score }}%</td>
                <td class="text-danger">{{ $emp['overdue'] }}</td>
                <td class="text-warning">{{ $emp['pending'] }}</td>
                <td>{{ $emp['in_progress'] }}</td>
                <td class="text-success">{{ $emp['completed'] }}</td>
                <td class="text-danger">{{ $emp['delayed'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
    function submitWeek(week) {
        if (!week) return;

        bootstrap.Modal
            .getInstance(document.getElementById('weekPickerModal'))
            .hide();

        window.location.href =
            `{{ route('report.table') }}?filter=week&week=${week}`;
    }
</script>
<script>
    function submitMonth(month) {
        if (!month) return;

        bootstrap.Modal
            .getInstance(document.getElementById('monthPickerModal'))
            .hide();

        window.location.href =
            `{{ route('report.table') }}?filter=month&month=${month}`;
    }
</script>
<script>
    function submitDay(date) {
        if (!date) return;
        bootstrap.Modal.getInstance(document.getElementById('dayPickerModal')).hide();
        window.location.href = `{{ route('report.table') }}?filter=day&date=${date}`;
    }

    function openWeek() {
        const d = new Date(), day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        const w = new Date(d.setDate(diff));
        window.location.href = `{{ route('report.table') }}?filter=week&week=${w.toISOString().slice(0,10)}`;
    }

    function openMonth() {
        document.getElementById('monthPicker').click();
    }

    function submitMonth(month) {
        if (month)
            window.location.href = `{{ route('report.table') }}?filter=month&month=${month}`;
    }
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endpush
