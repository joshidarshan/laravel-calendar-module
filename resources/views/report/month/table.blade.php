@extends('layouts.app')

@section('topbar-title', 'Month Report')

@section('content')
@section('topbar-buttons')
     @include('report._filterbar')
@endsection
{{-- TOP BAR : MONTH LABEL + FILTER BUTTONS --}}
{{-- REPORT TABLE --}}

{{-- MONTH NAVIGATION --}}
<div class="d-flex justify-content-between align-items-center mb-3">

    {{-- PREVIOUS MONTH --}}
    <a href="{{ route('report.month.table', ['month' => $prevMonth]) }}"
       class="btn btn-outline-secondary btn-sm">
        ⬅ {{ \Carbon\Carbon::createFromFormat('Y-m', $prevMonth)->format('M Y') }}
    </a>

    {{-- CURRENT MONTH --}}
    <div class="fw-bold fs-5">
        {{ $label }}
    </div>

    {{-- NEXT MONTH --}}
    <a href="{{ route('report.month.table', ['month' => $nextMonth]) }}"
       class="btn btn-outline-secondary btn-sm">
        {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('M Y') }} ➡
    </a>

</div>

<div class="report-card">
    <div class="report-title">Employee Performance (Month)</div>

    <table class="table align-middle text-center">
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
        <tbody>
            @forelse ($employeeWise as $emp)
                @php
                    $total = $emp['completed']
                           + $emp['pending']
                           + $emp['overdue']
                           + $emp['in_progress'];

                    $score = $total
                        ? round(($emp['completed'] / $total) * 100)
                        : 0;
                @endphp

                <tr>
                    <td class="text-start">{{ $emp['name'] }}</td>
                    <td>{{ $total }}</td>
                    <td class="fw-bold">{{ $score }}%</td>
                    <td class="text-danger">{{ $emp['overdue'] }}</td>
                    <td class="text-warning">{{ $emp['pending'] }}</td>
                    <td>{{ $emp['in_progress'] }}</td>
                    <td class="text-success">{{ $emp['completed'] }}</td>
                    <td class="text-danger">{{ $emp['delayed'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-muted py-4">
                        No data available for this month
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
