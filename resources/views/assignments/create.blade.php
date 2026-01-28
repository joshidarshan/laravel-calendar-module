@extends('layouts.app')

@section('topbar-title', isset($assignment) ? 'Edit Assignment' : 'Create Assignment')

@section('topbar-buttons')
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary btn-sm">Back</a>
@endsection

@section('content')
<div class="container" style="max-width: 700px;">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($assignment) ? 'Edit Assignment' : 'Create New Assignment' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($assignment) ? route('assignments.update', $assignment) : route('assignments.store') }}" method="POST">
                @csrf
                @if(isset($assignment))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="calendar_task_id" class="form-label"><strong>Task</strong></label>
                    <select name="calendar_task_id" id="calendar_task_id" class="form-control @error('calendar_task_id') is-invalid @enderror" required>
                        <option value="">Select a task...</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}" 
                                {{ (isset($assignment) && $assignment->calendar_task_id === $task->id) || old('calendar_task_id') == $task->id ? 'selected' : '' }}>
                                {{ $task->title }} ({{ $task->task_type }})
                            </option>
                        @endforeach
                    </select>
                    @error('calendar_task_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="assigned_to_user_id" class="form-label"><strong>Assign To</strong></label>
                    <select name="assigned_to_user_id" id="assigned_to_user_id" class="form-control @error('assigned_to_user_id') is-invalid @enderror" required>
                        <option value="">Select a user...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                {{ (isset($assignment) && $assignment->assigned_to_user_id === $user->id) || old('assigned_to_user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to_user_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label"><strong>Status</strong></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="pending" {{ (isset($assignment) && $assignment->status === 'pending') || old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ (isset($assignment) && $assignment->status === 'in_progress') || old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ (isset($assignment) && $assignment->status === 'completed') || old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ (isset($assignment) && $assignment->status === 'on_hold') || old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="cancelled" {{ (isset($assignment) && $assignment->status === 'cancelled') || old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="priority" class="form-label"><strong>Priority</strong></label>
                    <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror">
                        <option value="0" {{ (isset($assignment) && $assignment->priority === 0) || old('priority') == 0 ? 'selected' : '' }}>Normal</option>
                        <option value="1" {{ (isset($assignment) && $assignment->priority === 1) || old('priority') == 1 ? 'selected' : '' }}>High</option>
                        <option value="2" {{ (isset($assignment) && $assignment->priority === 2) || old('priority') == 2 ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="estimated_hours" class="form-label"><strong>Estimated Hours</strong></label>
                        <input type="number" name="estimated_hours" id="estimated_hours" class="form-control @error('estimated_hours') is-invalid @enderror" 
                            value="{{ isset($assignment) ? $assignment->estimated_hours : old('estimated_hours') }}" min="1">
                        @error('estimated_hours')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    @if(isset($assignment))
                    <div class="col-md-6 mb-3">
                        <label for="actual_hours" class="form-label"><strong>Actual Hours</strong></label>
                        <input type="number" name="actual_hours" id="actual_hours" class="form-control @error('actual_hours') is-invalid @enderror" 
                            value="{{ $assignment->actual_hours ?? old('actual_hours') }}" min="1">
                        @error('actual_hours')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    @endif
                </div>

                @if(isset($assignment))
                <div class="mb-3">
                    <label for="progress" class="form-label"><strong>Progress (%)</strong></label>
                    <input type="number" name="progress" id="progress" class="form-control @error('progress') is-invalid @enderror" 
                        value="{{ $assignment->progress ?? old('progress') }}" min="0" max="100">
                    @error('progress')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <div class="mb-3">
                    <label for="notes" class="form-label"><strong>Notes</strong></label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ isset($assignment) ? $assignment->notes : old('notes') }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ isset($assignment) ? 'Update' : 'Create' }} Assignment</button>
                    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
