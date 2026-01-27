{{-- =============================== --}}
{{-- DAY TABLE : resources/views/reports/day/table.blade.php --}}
{{-- =============================== --}}
@extends('layouts.app')
@section('topbar-title','Day Report')
@section('content')
@include('reports.partials.filter',['active'=>'day','view'=>'table'])
@include('reports.partials.table',['employeeWise'=>$employeeWise])
@endsection

{{-- =============================== --}}
{{-- WEEK TABLE : resources/views/reports/week/table.blade.php --}}
{{-- =============================== --}}
@extends('layouts.app')
@section('topbar-title','Week Report')
@section('content')
@include('reports.partials.filter',['active'=>'week','view'=>'table'])
@include('reports.partials.table',['employeeWise'=>$employeeWise])
@endsection

{{-- =============================== --}}
{{-- MONTH TABLE : resources/views/reports/month/table.blade.php --}}
{{-- =============================== --}}
@extends('layouts.app')
@section('topbar-title','Month Report')
@section('content')
@include('reports.partials.filter',['active'=>'month','view'=>'table'])
@include('reports.partials.table',['employeeWise'=>$employeeWise])
@endsection

{{-- =============================== --}}
{{-- ALL TABLE : resources/views/reports/all/table.blade.php --}}
{{-- =============================== --}}
@extends('layouts.app')
@section('topbar-title','All Report')
@section('content')
@include('reports.partials.filter',['active'=>'all','view'=>'table'])
@include('reports.partials.table',['employeeWise'=>$employeeWise])
@endsection

{{-- =============================== --}}
{{-- PARTIAL : resources/views/reports/partials/filter.blade.php --}}
{{-- =============================== --}}
<div class="filter-bar">
  <div class="filter-left">
    <a href="{{ route('reports.day.table') }}" class="btn btn-outline-primary {{ $active=='day'?'active':'' }}">Day</a>
    <a href="{{ route('reports.week.table') }}" class="btn btn-outline-primary {{ $active=='week'?'active':'' }}">Week</a>
    <a href="{{ route('reports.month.table') }}" class="btn btn-outline-primary {{ $active=='month'?'active':'' }}">Month</a>
    <a href="{{ route('reports.all.table') }}" class="btn btn-outline-secondary {{ $active=='all'?'active':'' }}">All</a>
  </div>
  <div class="filter-right">
    <a href="{{ route('reports.'.$active.'.chart') }}" class="btn btn-outline-primary">Charts</a>
    <a href="{{ route('reports.'.$active.'.table') }}" class="btn btn-primary">Table</a>
  </div>
</div>

{{-- =============================== --}}
{{-- PARTIAL : resources/views/reports/partials/table.blade.php --}}
{{-- =============================== --}}
<div class="report-card">
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
      @foreach($employeeWise as $emp)
        @php
          $total=$emp['completed']+$emp['pending']+$emp['overdue']+$emp['in_progress'];
          $score=$total?round(($emp['completed']/$total)*100):0;
        @endphp
        <tr>
          <td class="text-start">{{ $emp['name'] }}</td>
          <td>{{ $total }}</td>
          <td>{{ $score }}%</td>
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

---

## resources/views/report/all/table.blade.php

```blade
@extends('layouts.app')

@section('topbar-title', 'All Reports – Table')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
        font-family: 'Inter', sans-serif;
    }

    .filter-bar {
        background: #ffffff;
        border-radius: 14px;
        padding: 10px 14px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
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

    .summary-card {
        background: linear-gradient(135deg, #ffffff, #f9fafb);
        border-radius: 16px;
        padding: 18px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
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

    .report-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, .08);
    }

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
</style>
@endpush

@section('content')

{{-- FILTER BAR --}}
<div class="filter-bar">
    <div class="filter-left">
        <a href="{{ route('report.day.table') }}" class="btn btn-outline-primary">Day</a>
        <a href="{{ route('report.week.table') }}" class="btn btn-outline-primary">Week</a>
        <a href="{{ route('report.month.table') }}" class="btn btn-outline-primary">Month</a>
        <a href="{{ route('report.all.table') }}" class="btn btn-secondary">All</a>
    </div>

    <div class="filter-right">
        <a href="{{ route('report.all.chart') }}" class="btn btn-outline-primary">Chart</a>
        <a href="{{ route('report.all.table') }}" class="btn btn-primary">Table</a>
    </div>
</div>

@php
    $totalTasks = collect($employeeWise)->sum(fn($e) => $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress']);
    $completed  = collect($employeeWise)->sum('completed');
    $pending    = collect($employeeWise)->sum('pending');
    $overdue    = collect($employeeWise)->sum('overdue');
    $inProgress = collect($employeeWise)->sum('in_progress');
    $delayed    = collect($employeeWise)->sum('delayed');
@endphp

{{-- SUMMARY --}}
<div class="row g-4 mb-4">
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Total</div><div class="summary-value">{{ $totalTasks }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Overdue</div><div class="summary-value text-danger">{{ $overdue }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Pending</div><div class="summary-value text-warning">{{ $pending }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">In Progress</div><div class="summary-value">{{ $inProgress }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Completed</div><div class="summary-value text-success">{{ $completed }}</div></div></div>
    <div class="col-md-2"><div class="summary-card"><div class="summary-title">Delayed</div><div class="summary-value text-danger">{{ $delayed }}</div></div></div>
</div>

{{-- TABLE --}}
<div class="report-card">
    <h6 class="mb-3 fw-bold">Employee Performance (All)</h6>

    <table class="table align-middle text-center">
        <thead>
            <tr>
                <th class="text-start">Employee</th>
                <th>Total</th>
                <th>Completed</th>
                <th>Pending</th>
                <th>In Progress</th>
                <th>Overdue</th>
                <th>Delayed</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employeeWise as $emp)
                @php
                    $total = $emp['completed'] + $emp['pending'] + $emp['overdue'] + $emp['in_progress'];
                @endphp
                <tr>
                    <td class="text-start fw-semibold">{{ $emp['name'] }}</td>
                    <td>{{ $total }}</td>
                    <td class="text-success">{{ $emp['completed'] }}</td>
                    <td class="text-warning">{{ $emp['pending'] }}</td>
                    <td>{{ $emp['in_progress'] }}</td>
                    <td class="text-danger">{{ $emp['overdue'] }}</td>
                    <td class="text-danger">{{ $emp['delayed'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
```

