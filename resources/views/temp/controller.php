<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\TaskAssignment;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{

public function employeeReport(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $filter = $request->query('filter', 'day'); // day / week / month / all
    $offset = (int) $request->query('offset', 0);

    // Static sample data with date
    $employees = [
        ['employee' => 'Het Ladani', 'total' => 12, 'score' => 80, 'pending' => 2, 'progress' => 3, 'completed' => 6, 'overdue' => 1, 'date' => now()->format('Y-m-d')],
        ['employee' => 'Rudra Chabhadiya', 'total' => 15, 'score' => 90, 'pending' => 1, 'progress' => 4, 'completed' => 9, 'overdue' => 1, 'date' => now()->subDays(1)->format('Y-m-d')],
        ['employee' => 'Dhruv Solanki', 'total' => 10, 'score' => 75, 'pending' => 2, 'progress' => 2, 'completed' => 6, 'overdue' => 0, 'date' => now()->subDays(3)->format('Y-m-d')],
        ['employee' => 'Rahul Parihar', 'total' => 8, 'score' => 70, 'pending' => 1, 'progress' => 1, 'completed' => 6, 'overdue' => 0, 'date' => now()->subWeeks(1)->format('Y-m-d')],
        ['employee' => 'Yashmit Vithalani', 'total' => 11, 'score' => 85, 'pending' => 2, 'progress' => 3, 'completed' => 6, 'overdue' => 0, 'date' => now()->subMonths(1)->format('Y-m-d')],
    ];

    // Calculate date range based on filter + offset
    $from = $to = null;
    switch ($filter) {
        case 'day':
            $from = now()->addDays($offset)->startOfDay();
            $to = now()->addDays($offset)->endOfDay();
            $label = $from->format('d M Y');
            break;

        case 'week':
            $from = now()->addWeeks($offset)->startOfWeek();
            $to = now()->addWeeks($offset)->endOfWeek();
            $label = $from->format('d M') . ' - ' . $to->format('d M Y');
            break;

        case 'month':
            $from = now()->addMonths($offset)->startOfMonth();
            $to = now()->addMonths($offset)->endOfMonth();
            $label = $from->format('F Y');
            break;

        default:
            $label = 'All Time';
    }

    // Filter employees by date
    $data = array_filter($employees, function($e) use($from, $to) {
        $d = \Carbon\Carbon::parse($e['date']);
        return !$from || !$to || $d->between($from, $to);
    });

    $data = array_values($data); // reset keys

    return response()->json([
        'label' => $label,
        'data' => $data
    ]);
}


    // public function index(Request $request)
    // {
    //     return view('report.report-table');
    // }
    // // DataTable JSON
    // public function data(Request $request)
    // {
    //     $filter = $request->filter ?? 'day';
    //     [$from, $to, $label, $prev, $next] = $this->resolveRange($filter, $request);

    //     $data = $this->getEmployeeData($from, $to);

    //     return DataTables::of($data)
    //         ->addColumn('total', fn($e) => $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress'])
    //         ->addColumn('score', function ($e) {
    //             $total = $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress'];
    //             return $total ? round(($e['completed'] / $total) * 100) . '%' : '0%';
    //         })
    //         ->with([
    //             'label' => $label,
    //             'prev'  => $prev,
    //             'next'  => $next,
    //         ])
    //         ->make(true);
    // }

    // // Chart JSON
    // public function chartData(Request $request)
    // {
    //     $filter = $request->filter ?? 'day';
    //     [$from, $to, $label, $prev, $next] = $this->resolveRange($filter, $request);

    //     $assignTable = (new TaskAssignment())->getTable();
    //     $today = Carbon::today();
    //     $userMap = [];
    //     $userIds = [];

    //     $assignRows = DB::table($assignTable)
    //         ->select('assigned_to_user_id', 'status', 'target_date', DB::raw('count(*) as cnt'))
    //         ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
    //         ->groupBy('assigned_to_user_id', 'status', 'target_date')
    //         ->get();

    //     foreach ($assignRows as $r) {
    //         $uid = $r->assigned_to_user_id ?? 0;
    //         $userIds[] = $uid;

    //         // 🔹 Overdue only
    //         if ($r->status !== 'completed' && Carbon::parse($r->target_date)->lt($today)) {
    //             $userMap[$uid]['overdue'] = ($userMap[$uid]['overdue'] ?? 0) + (int)$r->cnt;
    //             $userMap[$uid]['delayed'] = ($userMap[$uid]['delayed'] ?? 0) + (int)$r->cnt; // chart uses delayed
    //         }
    //         // 🔹 Pending / In Progress (exclude overdue)
    //         elseif ($r->status === 'pending') {
    //             $userMap[$uid]['pending'] = ($userMap[$uid]['pending'] ?? 0) + (int)$r->cnt;
    //         } elseif ($r->status === 'in_progress') {
    //             $userMap[$uid]['in_progress'] = ($userMap[$uid]['in_progress'] ?? 0) + (int)$r->cnt;
    //         }

    //         // 🔹 Completed
    //         if ($r->status === 'completed') {
    //             $userMap[$uid]['completed'] = ($userMap[$uid]['completed'] ?? 0) + (int)$r->cnt;
    //         }
    //     }

    //     $userIds = array_values(array_unique($userIds));
    //     $users = $userIds ? User::whereIn('id', $userIds)->pluck('name', 'id')->toArray() : [];

    //     $employeeRecords = [];
    //     foreach ($userIds as $uid) {
    //         $employeeRecords[] = [
    //             'label' => $users[$uid] ?? 'N/A',
    //             'completed' => $userMap[$uid]['completed'] ?? 0,
    //             'pending' => $userMap[$uid]['pending'] ?? 0,
    //             'in_progress' => $userMap[$uid]['in_progress'] ?? 0,
    //             'overdue' => $userMap[$uid]['overdue'] ?? 0,
    //             'delayed' => $userMap[$uid]['delayed'] ?? 0,
    //         ];
    //     }

    //     // Sort by completed
    //     usort($employeeRecords, fn($a, $b) => $b['completed'] <=> $a['completed'] ?: strcmp($a['label'], $b['label']));

    //     // Arrays for chart
    //     $employeeLabels = array_column($employeeRecords, 'label');
    //     $employeeCompleted = array_column($employeeRecords, 'completed');
    //     $employeePending = array_column($employeeRecords, 'pending');
    //     $employeeOverdue = array_column($employeeRecords, 'overdue');
    //     $employeeInProgress = array_column($employeeRecords, 'in_progress');
    //     $employeeDelayed = array_column($employeeRecords, 'delayed');

    //     // Summary
    //     $summary = [
    //         'completed' => array_sum($employeeCompleted),
    //         'pending' => array_sum($employeePending),
    //         'overdue' => array_sum($employeeOverdue),
    //         'in_progress' => array_sum($employeeInProgress),
    //         'delayed' => array_sum($employeeDelayed),
    //         'not_completed' => array_sum($employeePending) + array_sum($employeeOverdue) + array_sum($employeeInProgress),
    //         'in_time' => max(array_sum($employeeCompleted) - array_sum($employeeDelayed), 0),
    //     ];

    //     return response()->json([
    //         'label' => $label,
    //         'prev' => $prev,
    //         'next' => $next,
    //         'summary' => $summary,
    //         'employee' => [
    //             'labels' => $employeeLabels,
    //             'completed' => $employeeCompleted,
    //             'pending' => $employeePending,
    //             'in_progress' => $employeeInProgress,
    //             'overdue' => $employeeOverdue,
    //             'delayed' => $employeeDelayed,
    //         ],
    //     ]);
    // }

    // // Range helper
    // private function resolveRange($filter, Request $request)
    // {
    //     $now = Carbon::now();

    //     if ($filter === 'day') {
    //         $date = $request->date ? Carbon::createFromFormat('Y-m-d', $request->date) : Carbon::today();
    //         return [
    //             $date->copy()->startOfDay(),
    //             $date->copy()->endOfDay(),
    //             $date->format('d M Y'),
    //             $date->copy()->subDay()->format('Y-m-d'),
    //             $date->copy()->addDay()->format('Y-m-d'),
    //         ];
    //     }

    //     if ($filter === 'week') {
    //         $week = $request->week ? Carbon::createFromFormat('Y-m-d', $request->week) : $now;
    //         $start = $week->copy()->startOfWeek();
    //         $end = $week->copy()->endOfWeek();
    //         return [
    //             $start,
    //             $end,
    //             'Week: ' . $start->format('d M') . ' - ' . $end->format('d M Y'),
    //             $start->copy()->subWeek()->format('Y-m-d'),
    //             $start->copy()->addWeek()->format('Y-m-d'),
    //         ];
    //     }

    //     if ($filter === 'month') {
    //         $month = $request->month ? Carbon::createFromFormat('Y-m-d', $request->month) : $now->copy()->startOfMonth();
    //         return [
    //             $month->copy()->startOfMonth(),
    //             $month->copy()->endOfMonth(),
    //             $month->format('F Y'),
    //             $month->copy()->subMonth()->startOfMonth()->format('Y-m-d'),
    //             $month->copy()->addMonth()->startOfMonth()->format('Y-m-d'),
    //         ];
    //     }

    //     return [null, null, 'All Time', null, null];
    // }

    // // Employee Data helper
    // private function getEmployeeData($from = null, $to = null)
    // {
    //     $assignTable = (new TaskAssignment())->getTable();
    //     $rows = DB::table($assignTable)
    //         ->select('assigned_to_user_id', 'status', 'target_date', DB::raw('count(*) as cnt'))
    //         ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
    //         ->groupBy('assigned_to_user_id', 'status', 'target_date')
    //         ->get();

    //     $map = [];
    //     $userIds = [];
    //     $today = Carbon::today();

    //     foreach ($rows as $r) {
    //         $uid = $r->assigned_to_user_id ?? 0;
    //         $userIds[] = $uid;

    //         // 🔹 Overdue only
    //         if ($r->status !== 'completed' && Carbon::parse($r->target_date)->lt($today)) {
    //             $map[$uid]['overdue'] = ($map[$uid]['overdue'] ?? 0) + $r->cnt;
    //             $map[$uid]['delayed'] = ($map[$uid]['delayed'] ?? 0) + $r->cnt;
    //         }
    //         // 🔹 Pending / In Progress (exclude overdue)
    //         elseif ($r->status === 'pending') {
    //             $map[$uid]['pending'] = ($map[$uid]['pending'] ?? 0) + $r->cnt;
    //         } elseif ($r->status === 'in_progress') {
    //             $map[$uid]['in_progress'] = ($map[$uid]['in_progress'] ?? 0) + $r->cnt;
    //         }

    //         // 🔹 Completed
    //         if ($r->status === 'completed') {
    //             $map[$uid]['completed'] = ($map[$uid]['completed'] ?? 0) + $r->cnt;
    //         }
    //     }

    //     $userIds = array_values(array_unique($userIds));
    //     $users = $userIds ? User::whereIn('id', $userIds)->pluck('name', 'id')->toArray() : [];

    //     $result = [];
    //     foreach ($userIds as $uid) {
    //         $result[] = [
    //             'employee' => $users[$uid] ?? 'N/A',
    //             'completed' => $map[$uid]['completed'] ?? 0,
    //             'pending' => $map[$uid]['pending'] ?? 0,
    //             'in_progress' => $map[$uid]['in_progress'] ?? 0,
    //             'overdue' => $map[$uid]['overdue'] ?? 0,
    //             'delayed' => $map[$uid]['delayed'] ?? 0,
    //         ];
    //     }

    //     return $result;
    // }
}
