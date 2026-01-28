<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

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
            ->addColumn('total', fn($e) =>
                $e['completed'] + $e['pending'] + $e['overdue'] + $e['in_progress']
            )
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

    public function chartData(Request $request)
    {
        $filter = $request->filter ?? 'day';

        [$from, $to, $label, $prev, $next] = $this->resolveRange($filter, $request);

        $data = $this->getEmployeeData($from, $to);

        return response()->json([
            'label' => $label,
            'prev'  => $prev,
            'next'  => $next,
            'summary' => [
                'completed'   => array_sum(array_column($data, 'completed')),
                'pending'     => array_sum(array_column($data, 'pending')),
                'overdue'     => array_sum(array_column($data, 'overdue')),
                'in_progress' => array_sum(array_column($data, 'in_progress')),
                'delayed'     => array_sum(array_column($data, 'delayed')),
            ]
        ]);
    }

   private function resolveRange($filter, Request $request)
{
    $now = Carbon::now();

    if ($filter === 'day') {
        $date = $request->date
            ? Carbon::createFromFormat('Y-m-d', $request->date)
            : Carbon::today();

        return [
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay(),
            $date->format('d M Y'),
            $date->copy()->subDay()->format('Y-m-d'),
            $date->copy()->addDay()->format('Y-m-d'),
        ];
    }

    if ($filter === 'week') {
        $week = $request->week
            ? Carbon::createFromFormat('Y-m-d', $request->week)
            : $now;

        $start = $week->copy()->startOfWeek();
        $end   = $week->copy()->endOfWeek();

        return [
            $start,
            $end,
            'Week: '.$start->format('d M').' - '.$end->format('d M Y'),
            $start->copy()->subWeek()->format('Y-m-d'),
            $start->copy()->addWeek()->format('Y-m-d'),
        ];
    }

    if ($filter === 'month') {
        $month = $request->month
            ? Carbon::createFromFormat('Y-m-d', $request->month)
            : $now->copy()->startOfMonth();

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


    private function getEmployeeData($from = null, $to = null)
    {
        $data = [
            ['name'=>'Het Ladani','completed'=>5,'pending'=>2,'overdue'=>1,'in_progress'=>3,'delayed'=>0,'date'=>'2026-05-26'],
            ['name'=>'Rudra Chabhadiya','completed'=>8,'pending'=>1,'overdue'=>0,'in_progress'=>2,'delayed'=>1,'date'=>'2026-01-27'],
            ['name'=>'Dhruv Solanki','completed'=>7,'pending'=>3,'overdue'=>2,'in_progress'=>1,'delayed'=>0,'date'=>'2026-01-25'],
            ['name'=>'Rahul Parihar','completed'=>6,'pending'=>2,'overdue'=>1,'in_progress'=>1,'delayed'=>1,'date'=>'2026-01-27'],
            ['name'=>'Yashmit Vithalani','completed'=>10,'pending'=>1,'overdue'=>0,'in_progress'=>0,'delayed'=>0,'date'=>'2026-01-27'],
            ['name'=>'Darshan Joshi','completed'=>4,'pending'=>3,'overdue'=>2,'in_progress'=>2,'delayed'=>1,'date'=>'2026-01-26'],
        ];

        if ($from && $to) {
            $data = array_filter($data, fn($e) =>
                Carbon::parse($e['date'])->between($from, $to)
            );
        }

        return array_values($data);
    }
}
