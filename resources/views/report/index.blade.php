@extends('layouts.app')

@push('styles')
<style>
    body { background:#f3f4f6; }
    .report-card{
        background:#fff;
        border-radius:14px;
        padding:16px;
        box-shadow:0 6px 16px rgba(0,0,0,.06);
    }
    .report-title{
        font-size:14px;
        font-weight:600;
        margin-bottom:10px;
    }
</style>
@endpush

@section('content')
<div class="d-flex gap-2 align-items-center mb-4">

    <!-- Day -->
    <button type="button" class="btn btn-outline-primary" onclick="openDay()">
        Day
    </button>
    <input type="date" id="dayPicker" class="d-none" onchange="submitDay(this.value)">

    <!-- Week -->
    <a href="{{ route('report.index', ['filter'=>'week']) }}"
       class="btn btn-outline-primary">
        Week
    </a>

    <!-- Month -->
    <button type="button" class="btn btn-outline-primary" onclick="openMonth()">
        Month
    </button>
    <input type="month" id="monthPicker" class="d-none" onchange="submitMonth(this.value)">

    <!-- All -->
    <a href="{{ route('report.index', ['filter'=>'all']) }}"
       class="btn btn-outline-secondary">
        All
    </a>

</div>

<div class="container-fluid">

    {{-- TOP DONUT CHARTS --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="report-card">
                <div class="report-title">Overdue / Pending / In Progress</div>
                <canvas id="chart1"></canvas>
            </div>
        </div>

        <div class="col-md-4">
            <div class="report-card">
                <div class="report-title">Completed vs Not</div>
                <canvas id="chart2"></canvas>
            </div>
        </div>

        <div class="col-md-4">
            <div class="report-card">
                <div class="report-title">In-Time vs Delayed</div>
                <canvas id="chart3"></canvas>
            </div>
        </div>
    </div>

    {{-- EMPLOYEE TABLE --}}
    <div class="report-card">
        <div class="report-title">Employee Wise Report</div>

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Completed</th>
                    <th>Pending</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employeeWise as $emp)
                <tr>
                    <td>{{ $emp['name'] }}</td>
                    <td class="text-success fw-semibold">{{ $emp['completed'] }}</td>
                    <td class="text-danger fw-semibold">{{ $emp['pending'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // PHP → JS data
    const summary = @json($summary);
    const employees = @json($employeeWise);

    // Chart 1
    new Chart(chart1,{
        type:'doughnut',
        data:{
            labels:['Overdue','Pending','In Progress'],
            datasets:[{
                data:[
                    summary.overdue,
                    summary.pending,
                    summary.in_progress
                ],
                backgroundColor:['#ef4444','#f59e0b','#eab308']
            }]
        }
    });

    // Chart 2
    new Chart(chart2,{
        type:'doughnut',
        data:{
            labels:['Completed','Not Completed'],
            datasets:[{
                data:[
                    summary.completed,
                    summary.not_completed
                ],
                backgroundColor:['#22c55e','#ef4444']
            }]
        }
    });

    // Chart 3
    new Chart(chart3,{
        type:'doughnut',
        data:{
            labels:['In-Time','Delayed'],
            datasets:[{
                data:[
                    summary.in_time,
                    summary.delayed
                ],
                backgroundColor:['#22c55e','#ef4444']
            }]
        }
    });
</script>
@endpush
