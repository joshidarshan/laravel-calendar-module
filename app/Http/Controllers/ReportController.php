<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function table(Request $request)
    {
        $range = $this->getDateRange($request);
        $employeeWise = $this->getEmployeeData($range['from'], $range['to']);

        return view('report.report-table', compact(
            'employeeWise'
        ) + $range);
    }

    public function charts(Request $request)
    {
        $range = $this->getDateRange($request);
        $employeeBar = $this->getEmployeeData($range['from'], $range['to']);

        $summary = [
            'completed'    => array_sum(array_column($employeeBar, 'completed')),
            'pending'      => array_sum(array_column($employeeBar, 'pending')),
            'overdue'      => array_sum(array_column($employeeBar, 'overdue')),
            'in_progress'  => array_sum(array_column($employeeBar, 'in_progress')),
            'delayed'      => array_sum(array_column($employeeBar, 'delayed')),
        ];

        $summary['not_completed'] =
            $summary['pending'] +
            $summary['overdue'] +
            $summary['in_progress'];

        $summary['in_time'] = $summary['completed'];

        $categoryBar = [
            'completed'   => $summary['completed'],
            'pending'     => $summary['pending'],
            'overdue'     => $summary['overdue'],
            'in_progress' => $summary['in_progress'],
        ];

        return view('report.report-charts', compact(
            'summary',
            'employeeBar',
            'categoryBar'
        ) + $range);
    }

    /* =========================
       DATE RANGE HANDLER
    ========================= */
    private function getDateRange(Request $request)
    {
        $filter = $request->filter ?? 'all';
        $from = $to = $label = null;

        try {

            if ($filter === 'day' && $request->date) {
                $date = Carbon::parse($request->date);
                $from = $date->copy()->startOfDay();
                $to   = $date->copy()->endOfDay();
                $label = $date->format('d M Y');
            }

            elseif ($filter === 'week' && $request->week) {
                $weekDate = Carbon::parse($request->week);
                $from = $weekDate->copy()->startOfWeek(Carbon::MONDAY);
                $to   = $weekDate->copy()->endOfWeek(Carbon::SUNDAY);
                $label = 'Week: ' . $from->format('d M') . ' - ' . $to->format('d M Y');
            }

            elseif ($filter === 'month' && $request->month) {
                $month = Carbon::parse($request->month);
                $from = $month->copy()->startOfMonth();
                $to   = $month->copy()->endOfMonth();
                $label = $month->format('F Y');
            }

        } catch (\Exception $e) {
            $filter = 'all';
        }

        return compact('filter', 'from', 'to', 'label');
    }

    /* =========================
       MOCK DATA (TEMP)
    ========================= */
    private function getEmployeeData($from = null, $to = null)
    {
        $data = [
            ['name'=>'Het Ladani','completed'=>5,'pending'=>2,'overdue'=>1,'in_progress'=>3,'delayed'=>0,'date'=>'2026-01-03'],
            ['name'=>'Rudra Chabhadiya','completed'=>8,'pending'=>1,'overdue'=>0,'in_progress'=>2,'delayed'=>1,'date'=>'2026-06-26'],
            ['name'=>'Dhruv Solanki','completed'=>7,'pending'=>3,'overdue'=>2,'in_progress'=>1,'delayed'=>0,'date'=>'2026-01-25'],
            ['name'=>'Rahul Parihar','completed'=>6,'pending'=>2,'overdue'=>1,'in_progress'=>1,'delayed'=>1,'date'=>'2026-01-27'],
            ['name'=>'Yashmit Vithalani','completed'=>10,'pending'=>1,'overdue'=>0,'in_progress'=>0,'delayed'=>0,'date'=>'2026-01-27'],
            ['name'=>'Darshan Joshi','completed'=>4,'pending'=>3,'overdue'=>2,'in_progress'=>2,'delayed'=>1,'date'=>'2026-01-26'],
            ['name'=>'Kritika Patel','completed'=>9,'pending'=>0,'overdue'=>1,'in_progress'=>1,'delayed'=>0,'date'=>'2026-01-27'],
            ['name'=>'Nirav Mehta','completed'=>3,'pending'=>4,'overdue'=>2,'in_progress'=>2,'delayed'=>1,'date'=>'2026-01-25'],
            ['name'=>'Priya Sharma','completed'=>7,'pending'=>2,'overdue'=>1,'in_progress'=>2,'delayed'=>0,'date'=>'2026-01-27'],
            ['name'=>'Amit Desai','completed'=>6,'pending'=>1,'overdue'=>0,'in_progress'=>3,'delayed'=>0,'date'=>'2026-01-26'],
        ];

        if ($from && $to) {
            $data = array_filter($data, function ($emp) use ($from, $to) {
                return Carbon::parse($emp['date'])->between($from, $to);
            });
        }

        return array_values($data);
    }
}
