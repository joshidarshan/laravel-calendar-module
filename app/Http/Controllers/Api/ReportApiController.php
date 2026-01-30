<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\TaskAssignment;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    public function tableData(Request $request)
    {
        $filter = $request->filter ?? 'day';
        [$from, $to, $label, $prev, $next] = $this->resolveRange($filter, $request);

        $data = $this->getEmployeeData($from, $to);

        return response()->json([
            'data' => array_map(function ($e) {
                $total = $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress'];
                return array_merge($e, [
                    'total' => $total,
                    'score' => $total ? round(($e['completed'] / $total) * 100) . '%' : '0%'
                ]);
            }, $data),
            'label' => $label,
            'prev'  => $prev,
            'next'  => $next,
        ]);
    }

    public function chartData(Request $request)
    {
        $filter = $request->filter ?? 'day';
        [$from, $to, $label, $prev, $next] = $this->resolveRange($filter, $request);

        $today = Carbon::today();
        $assignTable = (new TaskAssignment())->getTable();

        $rows = DB::table($assignTable)
            ->select('assigned_to_user_id', 'status', 'target_date', DB::raw('count(*) as cnt'))
            ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
            ->groupBy('assigned_to_user_id', 'status', 'target_date')
            ->get();

        $map = [];
        $userIds = [];

        foreach ($rows as $r) {
            $uid = $r->assigned_to_user_id ?? 0;
            $userIds[] = $uid;

            if ($r->status !== 'completed' && Carbon::parse($r->target_date)->lt($today)) {
                $map[$uid]['overdue'] = ($map[$uid]['overdue'] ?? 0) + $r->cnt;
                $map[$uid]['delayed'] = ($map[$uid]['delayed'] ?? 0) + $r->cnt;
            } elseif ($r->status === 'pending') {
                $map[$uid]['pending'] = ($map[$uid]['pending'] ?? 0) + $r->cnt;
            } elseif ($r->status === 'in_progress') {
                $map[$uid]['in_progress'] = ($map[$uid]['in_progress'] ?? 0) + $r->cnt;
            }

            if ($r->status === 'completed') {
                $map[$uid]['completed'] = ($map[$uid]['completed'] ?? 0) + $r->cnt;
            }
        }

        $users = User::whereIn('id', array_unique($userIds))->pluck('name', 'id')->toArray();

        $employees = [];
        foreach (array_unique($userIds) as $uid) {
            $employees[] = [
                'label' => $users[$uid] ?? 'N/A',
                'completed' => $map[$uid]['completed'] ?? 0,
                'pending' => $map[$uid]['pending'] ?? 0,
                'in_progress' => $map[$uid]['in_progress'] ?? 0,
                'overdue' => $map[$uid]['overdue'] ?? 0,
                'delayed' => $map[$uid]['delayed'] ?? 0,
            ];
        }

        $summary = [
            'completed' => array_sum(array_column($employees, 'completed')),
            'pending' => array_sum(array_column($employees, 'pending')),
            'in_progress' => array_sum(array_column($employees, 'in_progress')),
            'overdue' => array_sum(array_column($employees, 'overdue')),
            'delayed' => array_sum(array_column($employees, 'delayed')),
            'not_completed' =>
                array_sum(array_column($employees, 'pending')) +
                array_sum(array_column($employees, 'in_progress')) +
                array_sum(array_column($employees, 'overdue')),
            'in_time' =>
                max(array_sum(array_column($employees, 'completed')) -
                    array_sum(array_column($employees, 'delayed')), 0),
        ];

        return response()->json([
            'label' => $label,
            'prev' => $prev,
            'next' => $next,
            'summary' => $summary,
            'employee' => [
                'labels' => array_column($employees, 'label'),
                'completed' => array_column($employees, 'completed'),
                'pending' => array_column($employees, 'pending'),
                'in_progress' => array_column($employees, 'in_progress'),
                'overdue' => array_column($employees, 'overdue'),
                'delayed' => array_column($employees, 'delayed'),
            ],
        ]);
    }

    private function resolveRange($filter, Request $request)
    {
        $now = Carbon::now();

        if ($filter === 'day') {
            $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
            return [
                $date->startOfDay(),
                $date->endOfDay(),
                $date->format('d M Y'),
                $date->copy()->subDay()->format('Y-m-d'),
                $date->copy()->addDay()->format('Y-m-d'),
            ];
        }

      if ($filter === 'week') {
    $start = $now->startOfWeek();
    $end = $now->endOfWeek();
    return [$start, $end, 'Week', null, null];
}

if ($filter === 'month') {
    return [
        $now->startOfMonth(),
        $now->endOfMonth(),
        $now->format('F Y'),
        null,
        null
    ];
}


        return [null, null, 'All Time', null, null];
    }

    private function getEmployeeData($from, $to)
    {
        $assignTable = (new TaskAssignment())->getTable();
        $rows = DB::table($assignTable)
            ->select('assigned_to_user_id', 'status', 'target_date', DB::raw('count(*) as cnt'))
            ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
            ->groupBy('assigned_to_user_id', 'status', 'target_date')
            ->get();

        $map = [];
        $userIds = [];
        $today = Carbon::today();

        foreach ($rows as $r) {
            $uid = $r->assigned_to_user_id ?? 0;
            $userIds[] = $uid;

            if ($r->status !== 'completed' && Carbon::parse($r->target_date)->lt($today)) {
                $map[$uid]['overdue'] = ($map[$uid]['overdue'] ?? 0) + $r->cnt;
                $map[$uid]['delayed'] = ($map[$uid]['delayed'] ?? 0) + $r->cnt;
            } elseif ($r->status === 'pending') {
                $map[$uid]['pending'] = ($map[$uid]['pending'] ?? 0) + $r->cnt;
            } elseif ($r->status === 'in_progress') {
                $map[$uid]['in_progress'] = ($map[$uid]['in_progress'] ?? 0) + $r->cnt;
            }

            if ($r->status === 'completed') {
                $map[$uid]['completed'] = ($map[$uid]['completed'] ?? 0) + $r->cnt;
            }
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
