<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarTask;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarTaskController extends Controller
{
    public function index()
    {
        return response()->json(CalendarTask::all());
    }

    public function store(Request $request)
    {
        $task = CalendarTask::create($request->all());
        return response()->json($task, 201);
    }

    public function show($id)
    {
        return CalendarTask::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $task = CalendarTask::findOrFail($id);
        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy($id)
    {
        CalendarTask::findOrFail($id)->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    public function today()
    {
        return CalendarTask::whereDate('start_date', Carbon::today())->get();
    }

    public function week()
    {
        return CalendarTask::whereBetween('start_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->get();
    }

    public function month()
    {
        return CalendarTask::whereMonth('start_date', Carbon::now()->month)
                           ->whereYear('start_date', Carbon::now()->year)
                           ->get();
    }
}
