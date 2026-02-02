<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\TaskAssignment;

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

    // Determine date range based on filter
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

    // Fetch assignments
    $assignments = TaskAssignment::with('assignedUser')
        ->when($from && $to, fn($q) => $q->whereBetween('target_date', [$from, $to]))
        ->get();

    // Map per employee
    $map = [];

    foreach ($assignments as $task) {
        $uid = $task->assigned_to_user_id;

        // Initialize all fields if not exists
        if (!isset($map[$uid])) {
            $map[$uid] = [
                'employee' => $task->assignedUser->name ?? 'N/A',
                'total' => 0,
                'pending' => 0,
                'progress' => 0,
                'completed' => 0,
                'overdue' => 0,
                'score' => 0,
            ];
        }

        $map[$uid]['total'] += 1;

        // Count by status
        if ($task->status === 'pending') {
            $map[$uid]['pending'] += 1;
        } elseif ($task->status === 'in_progress') {
            $map[$uid]['progress'] += 1;
        } elseif ($task->status === 'completed') {
            $map[$uid]['completed'] += 1;
        }

        // Overdue (not completed and target_date < today)
        if ($task->status !== 'completed' && Carbon::parse($task->target_date)->lt(now())) {
            $map[$uid]['overdue'] += 1;
        }

        // Score = completed / total * 100
        $map[$uid]['score'] = round(($map[$uid]['completed'] / $map[$uid]['total']) * 100);
    }

    // Ensure employees with 0 tasks still show
    $allUsers = User::pluck('name', 'id');
    foreach ($allUsers as $id => $name) {
        if (!isset($map[$id])) {
            $map[$id] = [
                'employee' => $name,
                'total' => 0,
                'pending' => 0,
                'progress' => 0,
                'completed' => 0,
                'overdue' => 0,
                'score' => 0,
            ];
        }
    }

    // Convert map to array
// Convert map to array and remove employees with 0 total
$data = array_values(array_filter($map, fn($e) => $e['total'] > 0));

return response()->json([
    'label' => $label,
    'data' => $data
]);

}

}
