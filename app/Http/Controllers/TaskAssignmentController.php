<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use App\Models\CalendarTask;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskAssignmentController extends Controller
{
    /**
     * Display task assignments list
     */
    public function index()
    {
        $assignments = TaskAssignment::with(['task', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('assignments.index', compact('assignments'));
    }

    /**
     * Show assignment details
     */
    public function show(TaskAssignment $assignment)
    {
        $assignment->load(['task', 'assignedUser']);
        return view('assignments.show', compact('assignment'));
    }

    /**
     * Create new assignment
     */
    public function create()
    {
        $tasks = CalendarTask::all();
        $users = User::all();
        return view('assignments.create', compact('tasks', 'users'));
    }

    /**
     * Store new assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'calendar_task_id' => 'required|exists:calendar_tasks,id',
            'assigned_to_user_id' => 'required|exists:users,id',
            'status' => 'in:pending,in_progress,completed,on_hold,cancelled',
            'notes' => 'nullable|string',
            'priority' => 'integer|min:0|max:2',
            'estimated_hours' => 'nullable|integer|min:1',
        ]);

        $validated['assigned_at'] = now();

        $assignment = TaskAssignment::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'assignment' => $assignment]);
        }

        return redirect()->route('assignments.show', $assignment)->with('success', 'Assignment created successfully!');
    }

    /**
     * Edit assignment
     */
    public function edit(TaskAssignment $assignment)
    {
        $tasks = CalendarTask::all();
        $users = User::all();
        return view('assignments.edit', compact('assignment', 'tasks', 'users'));
    }

    /**
     * Update assignment
     */
    public function update(Request $request, TaskAssignment $assignment)
    {
        $validated = $request->validate([
            'calendar_task_id' => 'required|exists:calendar_tasks,id',
            'assigned_to_user_id' => 'required|exists:users,id',
            'status' => 'in:pending,in_progress,completed,on_hold,cancelled',
            'notes' => 'nullable|string',
            'priority' => 'integer|min:0|max:2',
            'estimated_hours' => 'nullable|integer|min:1',
            'actual_hours' => 'nullable|integer|min:1',
            'progress' => 'nullable|numeric|min:0|max:100',
        ]);

        $assignment->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'assignment' => $assignment]);
        }

        return redirect()->route('assignments.show', $assignment)->with('success', 'Assignment updated successfully!');
    }

    /**
     * Delete assignment
     */
    public function destroy(TaskAssignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('assignments.index')->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Start assignment
     */
    public function start(Request $request, TaskAssignment $assignment)
    {
        $assignment->start();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $assignment->status]);
        }

        return back()->with('success', 'Assignment started!');
    }

    /**
     * Complete assignment
     */
    public function complete(Request $request, TaskAssignment $assignment)
    {
        $validated = $request->validate([
            'actual_hours' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        if (isset($validated['notes'])) {
            $assignment->notes = $validated['notes'];
        }

        $assignment->complete($validated['actual_hours'] ?? null);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $assignment->status]);
        }

        return back()->with('success', 'Assignment completed!');
    }

    /**
     * Hold assignment
     */
    public function hold(Request $request, TaskAssignment $assignment)
    {
        $assignment->hold();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $assignment->status]);
        }

        return back()->with('success', 'Assignment put on hold!');
    }

    /**
     * Cancel assignment
     */
    public function cancel(Request $request, TaskAssignment $assignment)
    {
        $assignment->cancel();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $assignment->status]);
        }

        return back()->with('success', 'Assignment cancelled!');
    }

    /**
     * Update progress
     */
    public function updateProgress(Request $request, TaskAssignment $assignment)
    {
        $validated = $request->validate([
            'progress' => 'required|numeric|min:0|max:100'
        ]);

        $assignment->updateProgress($validated['progress']);

        return response()->json(['success' => true, 'progress' => $assignment->progress]);
    }

    /**
     * Get assignments for a task
     */
    public function getTaskAssignments($taskId)
    {
        $assignments = TaskAssignment::where('calendar_task_id', $taskId)
            ->with('assignedUser')
            ->get();

        return response()->json($assignments);
    }

    /**
     * Get user assignments
     */
    public function getUserAssignments(User $user)
    {
        $assignments = TaskAssignment::where('assigned_to_user_id', $user->id)
            ->with('task')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($assignments);
    }

    /**
     * Dashboard for assignments
     */
    public function dashboard()
    {
        $stats = [
            'total' => TaskAssignment::count(),
            'pending' => TaskAssignment::pending()->count(),
            'in_progress' => TaskAssignment::inProgress()->count(),
            'completed' => TaskAssignment::completed()->count(),
        ];

        $recentAssignments = TaskAssignment::with(['task', 'assignedUser'])
            ->latest()
            ->limit(10)
            ->get();

        $userStats = User::withCount([
            'assignments' => fn($q) => $q->pending(),
            'assignmentsInProgress' => fn($q) => $q->inProgress(),
            'assignmentsCompleted' => fn($q) => $q->completed(),
        ])->get();

        return view('assignments.dashboard', compact('stats', 'recentAssignments', 'userStats'));
    }
}
