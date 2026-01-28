@extends('layouts.app')

@section('topbar-title', 'Assignment Details')

@section('topbar-buttons')
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary btn-sm">Back</a>
    <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-warning btn-sm">Edit</a>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $assignment->task->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label><strong>Assigned To:</strong></label>
                            <p>{{ $assignment->assignedUser->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Status:</strong></label>
                            <p>
                                <span class="badge bg-{{ $assignment->status === 'completed' ? 'success' : ($assignment->status === 'in_progress' ? 'info' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label><strong>Priority:</strong></label>
                            <p>
                                <span class="badge" style="background-color: {{ $assignment->getPriorityColor() }}">
                                    {{ $assignment->getPriorityLabel() }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Progress:</strong></label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $assignment->progress }}%">
                                    {{ $assignment->progress }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label><strong>Estimated Hours:</strong></label>
                            <p>{{ $assignment->estimated_hours ?? 'Not set' }} hours</p>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Actual Hours:</strong></label>
                            <p>{{ $assignment->actual_hours ?? 'Not logged' }} hours</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label><strong>Assigned At:</strong></label>
                            <p>{{ $assignment->assigned_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label><strong>Started At:</strong></label>
                            <p>{{ $assignment->started_at?->format('M d, Y H:i') ?? 'Not started' }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label><strong>Completed At:</strong></label>
                        <p>{{ $assignment->completed_at?->format('M d, Y H:i') ?? 'Not completed' }}</p>
                    </div>

                    <div class="mb-3">
                        <label><strong>Notes:</strong></label>
                        <p>{{ $assignment->notes ?? 'No notes' }}</p>
                    </div>

                    <div class="mb-3">
                        <label><strong>Task Description:</strong></label>
                        <p>{{ $assignment->task->description ?? 'No description' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    @if($assignment->status === 'pending')
                        <form action="{{ route('assignments.start', $assignment) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-block w-100">Start Assignment</button>
                        </form>
                    @elseif($assignment->status === 'in_progress')
                        <form action="{{ route('assignments.complete', $assignment) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <div class="mb-2">
                                <input type="number" name="actual_hours" placeholder="Actual hours" class="form-control form-control-sm" min="0">
                            </div>
                            <button type="submit" class="btn btn-success btn-block w-100">Mark Complete</button>
                        </form>
                        <form action="{{ route('assignments.hold', $assignment) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-block w-100">Put on Hold</button>
                        </form>
                    @endif

                    @if($assignment->status !== 'completed')
                        <form action="{{ route('assignments.cancel', $assignment) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-block w-100">Cancel</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Update Progress</h5>
                </div>
                <div class="card-body">
                    <div id="progressForm">
                        <input type="range" class="form-range" id="progressSlider" min="0" max="100" value="{{ $assignment->progress }}" step="5">
                        <div class="text-center mt-2">
                            <span id="progressValue">{{ $assignment->progress }}%</span>
                        </div>
                        <button id="updateProgressBtn" class="btn btn-primary btn-sm w-100 mt-2">Update Progress</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('progressSlider').addEventListener('input', function() {
    document.getElementById('progressValue').textContent = this.value + '%';
});

document.getElementById('updateProgressBtn').addEventListener('click', function() {
    const progress = document.getElementById('progressSlider').value;
    fetch('{{ route("assignments.updateProgress", $assignment) }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ progress: progress })
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Progress updated successfully!');
        }
    });
});
</script>
@endsection
