    @extends('layouts.app')

    @section('topbar-title', isset($assignment) ? 'Edit Assignment' : 'Create Assignment')

    @section('topbar-buttons')
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">Back</a>
    @endsection

    @section('content')
    <div class="container" style="max-width: 800px;">
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

                    {{-- Task & Assigned To Side by Side --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="calendar_task_id" class="form-label"><strong>Task</strong></label>
                            <select name="calendar_task_id" id="calendar_task_id"
                                    class="form-select form-select-sm @error('calendar_task_id') is-invalid @enderror" required>
                                <option value="">Select a task...</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}"
                                        {{ (isset($assignment) && $assignment->calendar_task_id === $task->id) || old('calendar_task_id') == $task->id ? 'selected' : '' }}>
                                        {{ $task->title }} ({{ $task->task_type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('calendar_task_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="assigned_to_user_id" class="form-label"><strong>Assign To</strong></label>
                            <select name="assigned_to_user_id" id="assigned_to_user_id"
                                    class="form-select form-select-sm @error('assigned_to_user_id') is-invalid @enderror" required>
                                <option value="">Select a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ (isset($assignment) && $assignment->assigned_to_user_id === $user->id) || old('assigned_to_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to_user_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Status & Priority Side by Side --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label"><strong>Status</strong></label>
                            <select name="status" id="status" class="form-select form-select-sm @error('status') is-invalid @enderror">
                                <option value="pending" {{ (isset($assignment) && $assignment->status==='pending') || old('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ (isset($assignment) && $assignment->status==='in_progress') || old('status')=='in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ (isset($assignment) && $assignment->status==='completed') || old('status')=='completed' ? 'selected' : '' }}>Completed</option>
                                <option value="on_hold" {{ (isset($assignment) && $assignment->status==='on_hold') || old('status')=='on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="cancelled" {{ (isset($assignment) && $assignment->status==='cancelled') || old('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="priority" class="form-label"><strong>Priority</strong></label>
                            <select name="priority" id="priority" class="form-select form-select-sm @error('priority') is-invalid @enderror">
                                <option value="0" {{ (isset($assignment) && $assignment->priority===0) || old('priority')==0 ? 'selected' : '' }}>Normal</option>
                                <option value="1" {{ (isset($assignment) && $assignment->priority===1) || old('priority')==1 ? 'selected' : '' }}>High</option>
                                <option value="2" {{ (isset($assignment) && $assignment->priority===2) || old('priority')==2 ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Estimated Hours & Target Date Side by Side --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="estimated_hours" class="form-label"><strong>Estimated Hours</strong></label>
                            <input type="number" name="estimated_hours" id="estimated_hours"
                                class="form-control form-control-sm @error('estimated_hours') is-invalid @enderror"
                                value="{{ isset($assignment) ? $assignment->estimated_hours : old('estimated_hours') }}" min="1">
                            @error('estimated_hours')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="target_date" class="form-label"><strong>Target Completion Date</strong></label>
                            <input type="date" name="target_date" id="target_date"
                                class="form-control form-control-sm @error('target_date') is-invalid @enderror"
                                value="{{ isset($assignment) ? optional($assignment->target_date)->format('Y-m-d') : old('target_date') }}">
                            @error('target_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Actual Hours & Progress Side by Side (if editing) --}}
                    @if(isset($assignment))
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="actual_hours" class="form-label"><strong>Actual Hours</strong></label>
                            <input type="number" name="actual_hours" id="actual_hours"
                                class="form-control form-control-sm @error('actual_hours') is-invalid @enderror"
                                value="{{ $assignment->actual_hours ?? old('actual_hours') }}" min="1">
                            @error('actual_hours')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="progress" class="form-label"><strong>Progress (%)</strong></label>
                            <input type="number" name="progress" id="progress"
                                class="form-control form-control-sm @error('progress') is-invalid @enderror"
                                value="{{ $assignment->progress ?? old('progress') }}" min="0" max="100">
                            @error('progress')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    @endif

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label for="notes" class="form-label"><strong>Notes</strong></label>
                        <textarea name="notes" id="notes" rows="" class="form-control form-control-sm @error('notes') is-invalid @enderror">{{ isset($assignment) ? $assignment->notes : old('notes') }}</textarea>
                        @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">{{ isset($assignment) ? 'Update' : 'Create' }} Assignment</button>
                        <a href="{{ url()->previous()}}" class="btn btn-secondary btn-sm">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @endsection

    @push('styles')
    <style>
    /* Compact modern form styling */
    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
    }

    .form-control, .form-select {
        height: calc(1.6em + 0.5rem + 2px);
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }

    textarea.form-control {
        padding: 0.35rem 0.5rem;
    }

    .card-body {
        padding: 1rem;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.4em;
    }
    </style>
    @endpush
