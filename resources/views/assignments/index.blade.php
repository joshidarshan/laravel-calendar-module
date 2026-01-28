@extends('layouts.app')

@section('topbar-title', 'Task Assignments')

@section('topbar-buttons')
    <a href="{{ route('assignments.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> New Assignment
    </a>
    <a href="{{ route('assignments.dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-chart-bar"></i> Dashboard
    </a>
@endsection

@section('content')
<div class="container-fluid">
    @if($assignments->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Task</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Progress</th>
                    <th>Estimated Hours</th>
                    <th>Actual Hours</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $assignment)
                <tr>
                    <td><strong>{{ $assignment->task->title }}</strong></td>
                    <td>{{ $assignment->assignedUser->name }}</td>
                    <td>
                        <span class="badge bg-{{ $assignment->status === 'completed' ? 'success' : ($assignment->status === 'in_progress' ? 'info' : ($assignment->status === 'on_hold' ? 'warning' : 'secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background-color: {{ $assignment->getPriorityColor() }}">
                            {{ $assignment->getPriorityLabel() }}
                        </span>
                    </td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $assignment->progress }}%">
                                {{ $assignment->progress }}%
                            </div>
                        </div>
                    </td>
                    <td>{{ $assignment->estimated_hours ?? '-' }} h</td>
                    <td>{{ $assignment->actual_hours ?? '-' }} h</td>
                    <td>
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $assignments->links() }}
    </div>
    @else
    <div class="alert alert-info text-center">
        <p>No assignments yet. <a href="{{ route('assignments.create') }}">Create one now</a></p>
    </div>
    @endif
</div>
@endsection
