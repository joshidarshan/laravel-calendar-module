<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'today');

        if ($filter === 'today') {
            $from = Carbon::today();
            $to   = Carbon::today()->endOfDay();
        } elseif ($filter === 'month') {
            $from = Carbon::now()->startOfMonth();
            $to   = Carbon::now()->endOfMonth();
        } elseif ($filter === 'custom') {
            $from = Carbon::parse($request->from);
            $to   = Carbon::parse($request->to);
        }

        // 🔹 Static data (later DB query)
        $summary = [
            'overdue'       => 680,
            'pending'       => 33,
            'in_progress'   => 1,
            'completed'     => 12144,
            'not_completed' => 714,
            'in_time'       => 7396,
            'delayed'       => 4748,
        ];

        $employeeWise = [
            ['name' => 'JAY Lende', 'completed' => 571, 'pending' => 34],
            ['name' => 'Darshan Joshi', 'completed' => 526, 'pending' => 3],
            ['name' => 'MAN Kaushik', 'completed' => 473, 'pending' => 50],
            ['name' => 'Om Shishodia', 'completed' => 420, 'pending' => 15],
        ];

        return view('report.index', compact(
            'summary',
            'employeeWise',
            'filter',
            'from',
            'to'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
