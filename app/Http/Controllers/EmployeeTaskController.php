<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeTask;

class EmployeeTaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string',
            'task_name'     => 'required|string',
            'completed'     => 'integer|min:0',
            'pending'       => 'integer|min:0',
            'overdue'       => 'integer|min:0',
            'in_progress'   => 'integer|min:0',
            'delayed'       => 'integer|min:0',
            'task_date'     => 'nullable|date'
        ]);

        $task = EmployeeTask::create($validated);

        return response()->json(['success' => true, 'task' => $task]);
    }

    public function update(Request $request, EmployeeTask $task)
    {
        $validated = $request->validate([
            'task_name'     => 'required|string',
            'completed'     => 'integer|min:0',
            'pending'       => 'integer|min:0',
            'overdue'       => 'integer|min:0',
            'in_progress'   => 'integer|min:0',
            'delayed'       => 'integer|min:0',
            'task_date'     => 'nullable|date'
        ]);

        $task->update($validated);

        return response()->json(['success' => true, 'task' => $task]);
    }
}
