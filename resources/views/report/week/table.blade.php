@extends('layouts.app')

@section('topbar-title', 'Day Report')
@section('topbar-buttons')
     @include('report._filterbar')
@endsection
{{-- TOP NAV 
@section('content')
(PREV / DATE / NEXT) --}}
<div class="d-flex justify-content-between align-items-center mb-3">

    {{-- PREVIOUS DAY --}}
    <a href="{{ route('report.day.table', ['date' => $prevDate]) }}"
       class="btn btn-outline-secondary">
        ⬅ {{ \Carbon\Carbon::parse($prevDate)->format('d M') }}
    </a>

    {{-- CURRENT DAY --}}
    <div class="fw-bold fs-5">
        {{ $label }}
    </div>

    {{-- NEXT DAY --}}
    <a href="{{ route('report.day.table', ['date' => $nextDate]) }}"
       class="btn btn-outline-secondary">
        {{ \Carbon\Carbon::parse($nextDate)->format('d M') }} ➡
    </a>

</div>

{{-- TABLE --}}
<div class="report-card">
    <div class="report-title">Employee Performance (Day)</div>

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
            @forelse ($employeeWise as $emp)
                @php
                    $total = $emp['completed'] + $emp['pending'] + $emp['overdue'] + $emp['in_progress'];
                    $score = $total ? round(($emp['completed'] / $total) * 100) : 0;
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
                        No data for this day
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
