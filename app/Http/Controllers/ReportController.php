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
    public function index(Request $request)
    {
        return view('report.report-table');
    }

    public function data(Request $request)
    {
        $filter = $request->filter ?? 'day';
        [$from, $to, $label, $prev, $next] = $this->resolveRange($filter, $request);

        $data = $this->getEmployeeData($from, $to);

        return DataTables::of($data)
            ->addColumn('total', fn($e) => $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress'])
            ->addColumn('score', function ($e) {
                $total = $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress'];
                return $total ? round(($e['completed'] / $total) * 100) . '%' : '0%';
            })
            ->with([
                'label' => $label,
                'prev'  => $prev,
                'next'  => $next,
            ])
            ->make(true);
    }

    /**
     * Chart data: summary + employee arrays + category arrays
     */
    public function chartData(Request $request)
    {
        $filter = $request->filter ?? 'day';
        [$from, $to, $label, $prev, $next] = $this->resolveRange($filter, $request);

        $assignTable = (new TaskAssignment())->getTable();

        // Employee aggregation (group by assigned_to_user_id, status)
        $assignRows = DB::table($assignTable)
            ->select('assigned_to_user_id', 'status', DB::raw('count(*) as cnt'))
            ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
            ->groupBy('assigned_to_user_id', 'status')
            ->get();

        $userMap = [];
        $userIds = [];
        foreach ($assignRows as $r) {
            $uid = $r->assigned_to_user_id ?? 0;
            $userIds[] = $uid;
            $userMap[$uid][$r->status] = (int)$r->cnt;
        }
        $userIds = array_values(array_unique($userIds));
        $users = $userIds ? User::whereIn('id', $userIds)->pluck('name', 'id')->toArray() : [];

        $employeeRecords = [];
        foreach ($userIds as $uid) {
            $employeeRecords[] = [
                'label' => $users[$uid] ?? 'N/A',
                'completed' => $userMap[$uid]['completed'] ?? 0,
                'pending' => $userMap[$uid]['pending'] ?? 0,
                'overdue' => $userMap[$uid]['overdue'] ?? 0,
                'in_progress' => $userMap[$uid]['in_progress'] ?? 0,
                'delayed' => $userMap[$uid]['delayed'] ?? 0,
            ];
        }

        usort($employeeRecords, fn($a, $b) => $b['completed'] <=> $a['completed'] ?: strcmp($a['label'], $b['label']));

        $employeeLabels = array_column($employeeRecords, 'label');
        $employeeCompleted = array_column($employeeRecords, 'completed');
        $employeePending = array_column($employeeRecords, 'pending');
        $employeeOverdue = array_column($employeeRecords, 'overdue');
        $employeeInProgress = array_column($employeeRecords, 'in_progress');
        $employeeDelayed = array_column($employeeRecords, 'delayed');

        // Category aggregation: try multiple sources in order, stop when we get data
        $categoryLabels = $categoryCompleted = $categoryPending = $categoryOverdue = $categoryInProgress = $categoryDelayed = [];

        $tryBuild = function ($rows) use (&$categoryLabels, &$categoryCompleted, &$categoryPending, &$categoryOverdue, &$categoryInProgress, &$categoryDelayed) {
            if ($rows->isEmpty()) return false;
            $grouped = $rows->groupBy('category');
            foreach ($grouped as $cat => $items) {
                $categoryLabels[] = $cat ?? 'Uncategorized';
                $counts = $items->pluck('cnt', 'status')->toArray();
                $categoryCompleted[] = (int)($counts['completed'] ?? 0);
                $categoryPending[] = (int)($counts['pending'] ?? 0);
                $categoryOverdue[] = (int)($counts['overdue'] ?? 0);
                $categoryInProgress[] = (int)($counts['in_progress'] ?? 0);
                $categoryDelayed[] = (int)($counts['delayed'] ?? 0);
            }
            return count($categoryLabels) > 0;
        };

        // Source A: task_assignments.category (string)
        if (Schema::hasColumn($assignTable, 'category')) {
            $rows = DB::table($assignTable)
                ->select(DB::raw("COALESCE(category, 'Uncategorized') as category"), 'status', DB::raw('count(*) as cnt'))
                ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
                ->groupBy(DB::raw("COALESCE(category, 'Uncategorized')"), 'status')
                ->get();
            if ($tryBuild($rows)) { /* done */ }
        }

        // Source B: task_assignments.category_id -> categories.name
        if (empty($categoryLabels) && Schema::hasColumn($assignTable, 'category_id') && Schema::hasTable('categories')) {
            $rows = DB::table($assignTable)
                ->join('categories', 'categories.id', '=', "$assignTable.category_id")
                ->select(DB::raw("COALESCE(categories.name, 'Uncategorized') as category"), "$assignTable.status as status", DB::raw('count(*) as cnt'))
                ->when($from && $to, fn($q) => $q->whereBetween("$assignTable.target_date", [$from, $to]))
                ->groupBy('category', 'status')
                ->get();
            if ($tryBuild($rows)) { /* done */ }
        }

        // Source C: task_id -> tasks.category (string)
        if (empty($categoryLabels) && Schema::hasColumn($assignTable, 'task_id') && Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'category')) {
            $rows = DB::table($assignTable)
                ->join('tasks', 'tasks.id', '=', "$assignTable.task_id")
                ->select(DB::raw("COALESCE(tasks.category, 'Uncategorized') as category"), "$assignTable.status as status", DB::raw('count(*) as cnt'))
                ->when($from && $to, fn($q) => $q->whereBetween("$assignTable.target_date", [$from, $to]))
                ->groupBy('category', 'status')
                ->get();
            if ($tryBuild($rows)) { /* done */ }
        }

        // Source D: task_id -> tasks.category_id -> categories.name
        if (empty($categoryLabels)
            && Schema::hasColumn($assignTable, 'task_id')
            && Schema::hasTable('tasks')
            && Schema::hasColumn('tasks', 'category_id')
            && Schema::hasTable('categories')
        ) {
            $rows = DB::table($assignTable)
                ->join('tasks', 'tasks.id', '=', "$assignTable.task_id")
                ->join('categories', 'categories.id', '=', 'tasks.category_id')
                ->select(DB::raw("COALESCE(categories.name, 'Uncategorized') as category"), "$assignTable.status as status", DB::raw('count(*) as cnt'))
                ->when($from && $to, fn($q) => $q->whereBetween("$assignTable.target_date", [$from, $to]))
                ->groupBy('category', 'status')
                ->get();
            if ($tryBuild($rows)) { /* done */ }
        }

        // Pad category arrays to same length (Chart.js expects consistent arrays)
        $padTo = max(0, count($categoryLabels));
        $pad = fn($arr) => array_pad($arr, $padTo, 0);
        $categoryCompleted = $pad($categoryCompleted);
        $categoryPending = $pad($categoryPending);
        $categoryOverdue = $pad($categoryOverdue);
        $categoryInProgress = $pad($categoryInProgress);
        $categoryDelayed = $pad($categoryDelayed);

        // Summary totals
        $summary = [
            'completed' => array_sum($employeeCompleted),
            'pending' => array_sum($employeePending),
            'overdue' => array_sum($employeeOverdue),
            'in_progress' => array_sum($employeeInProgress),
            'delayed' => array_sum($employeeDelayed),
            'not_completed' => array_sum($employeePending) + array_sum($employeeOverdue) + array_sum($employeeInProgress),
            'in_time' => max(array_sum($employeeCompleted) - array_sum($employeeDelayed), 0),
        ];

        return response()->json([
            'label' => $label,
            'prev' => $prev,
            'next' => $next,
            'summary' => $summary,
            'employee' => [
                'labels' => $employeeLabels,
                'completed' => $employeeCompleted,
                'pending' => $employeePending,
                'overdue' => $employeeOverdue,
                'in_progress' => $employeeInProgress,
                'delayed' => $employeeDelayed,
            ],
            'category' => [
                'labels' => $categoryLabels,
                'completed' => $categoryCompleted,
                'pending' => $categoryPending,
                'overdue' => $categoryOverdue,
                'in_progress' => $categoryInProgress,
                'delayed' => $categoryDelayed,
            ]
        ]);
    }

    private function resolveRange($filter, Request $request)
    {
        $now = Carbon::now();

        if ($filter === 'day') {
            $date = $request->date ? Carbon::createFromFormat('Y-m-d', $request->date) : Carbon::today();
            return [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
                $date->format('d M Y'),
                $date->copy()->subDay()->format('Y-m-d'),
                $date->copy()->addDay()->format('Y-m-d'),
            ];
        }

        if ($filter === 'week') {
            $week = $request->week ? Carbon::createFromFormat('Y-m-d', $request->week) : $now;
            $start = $week->copy()->startOfWeek();
            $end = $week->copy()->endOfWeek();
            return [
                $start,
                $end,
                'Week: ' . $start->format('d M') . ' - ' . $end->format('d M Y'),
                $start->copy()->subWeek()->format('Y-m-d'),
                $start->copy()->addWeek()->format('Y-m-d'),
            ];
        }

        if ($filter === 'month') {
            $month = $request->month ? Carbon::createFromFormat('Y-m-d', $request->month) : $now->copy()->startOfMonth();
            return [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth(),
                $month->format('F Y'),
                $month->copy()->subMonth()->startOfMonth()->format('Y-m-d'),
                $month->copy()->addMonth()->startOfMonth()->format('Y-m-d'),
            ];
        }

        return [null, null, 'All Time', null, null];
    }

    /**
     * Efficient DB-based employee data used by DataTable
     */
    private function getEmployeeData($from = null, $to = null)
    {
        $assignTable = (new TaskAssignment())->getTable();
        $rows = DB::table($assignTable)
            ->select('assigned_to_user_id', 'status', DB::raw('count(*) as cnt'))
            ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
            ->groupBy('assigned_to_user_id', 'status')
            ->get();

        $map = []; $userIds = [];
        foreach ($rows as $r) {
            $uid = $r->assigned_to_user_id ?? 0;
            $userIds[] = $uid;
            $map[$uid][$r->status] = (int)$r->cnt;
        }
        $userIds = array_values(array_unique($userIds));
        $users = $userIds ? User::whereIn('id', $userIds)->pluck('name', 'id')->toArray() : [];

        $result = [];
        foreach ($userIds as $uid) {
            $result[] = [
                'employee' => $users[$uid] ?? 'N/A',
                'completed' => $map[$uid]['completed'] ?? 0,
                'pending' => $map[$uid]['pending'] ?? 0,
                'in_progress' => $map[$uid]['in_progress'] ?? 0,
                'overdue' => $map[$uid]['overdue'] ?? 0,
                'delayed' => $map[$uid]['delayed'] ?? 0,
            ];
        }

        return $result;
    }
}