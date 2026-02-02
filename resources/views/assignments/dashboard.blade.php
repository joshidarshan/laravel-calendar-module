@extends('layouts.app')

@section('topbar-title', 'Task Dashboard')

@section('topbar-buttons')
    <a href="{{ route('assignments.create') }}" class="btn btn-primary btn-sm rounded-pill me-2">
        <i class="fas fa-plus"></i> New Assignment
    </a>
    {{-- <a href="{{ route('assignments.index') }}" class="btn btn-secondary btn-sm rounded-pill">
        <i class="fas fa-list"></i> All Assignments
    </a> --}}
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        @php
            $cards = [
                ['title' => 'Total Assignments', 'count' => $stats['total'], 'color' => 'primary', 'icon' => 'fas fa-tasks'],
                ['title' => 'Pending', 'count' => $stats['pending'], 'color' => 'info', 'icon' => 'fas fa-clock'],
                ['title' => 'In Progress', 'count' => $stats['in_progress'], 'color' => 'warning', 'icon' => 'fas fa-spinner'],
                ['title' => 'Completed', 'count' => $stats['completed'], 'color' => 'success', 'icon' => 'fas fa-check-circle'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="{{ $card['icon'] }} fa-2x text-{{ $card['color'] }}"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">{{ $card['title'] }}</h6>
                        <h3 class="mb-0 text-{{ $card['color'] }}">{{ $card['count'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <!-- Recent Assignments -->
        {{-- <div class="col-lg-8 mb-4">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Assignments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAssignments as $assignment)
                                <tr onclick="window.location='{{ route('assignments.show', $assignment) }}'" style="cursor: pointer;">
                                    <td><strong>{{ Str::limit($assignment->task->title, 40) }}</strong></td>
                                    <td>{{ $assignment->assignedUser->name }}</td>
                                    <td>
                                        <span class="badge {{ $assignment->status === 'completed' ? 'bg-success' : ($assignment->status === 'in_progress' ? 'bg-info' : 'bg-secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill" style="background-color: {{ $assignment->getPriorityColor() }}; color: #fff;">
                                            {{ $assignment->getPriorityLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress rounded-pill" style="height: 18px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $assignment->progress }}%; 
                                                background-color: {{ $assignment->progress < 50 ? '#f44336' : ($assignment->progress < 80 ? '#ff9800' : '#4caf50') }};">
                                                {{ $assignment->progress }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
<table id="recentTable" class="table table-hover mb-0 align-middle w-100">
    <thead class="table-light">
        <tr>
            <th>Task</th>
            <th>Assigned To</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Progress</th>
            <th>Estimated Hours</th> <!-- New -->
            <th>Actual Hours</th>    <!-- New -->
            <th style="width: 18%">Actions</th>
        </tr>
    </thead>
</table>


        <!-- Team Performance -->
        {{-- <div class="col-lg-4 mb-4">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Team Performance</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Member</th>
                                    <th class="text-center">Pending</th>
                                    <th class="text-center">In Progress</th>
                                    <th class="text-center">Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userStats as $user)
                                <tr>
                                    <td><strong>{{ Str::limit($user->name, 18) }}</strong></td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $user->assignments_count ?? 0 }}</span></td>
                                    <td class="text-center"><span class="badge bg-warning">{{ $user->assignmentsInProgress_count ?? 0 }}</span></td>
                                    <td class="text-center"><span class="badge bg-success">{{ $user->assignmentsCompleted_count ?? 0 }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection
@push('scripts')
<script>
$('#recentTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('assignments.recent.data') }}",
    columns: [
        { data: 'task', orderable:false },
        { data: 'assigned_to' },
        { data: 'status', orderable:false },
        { data: 'priority', orderable:false },
        { data: 'progress', orderable:false },
        { data: 'estimated_hours', orderable:true }, // New
        { data: 'actual_hours', orderable:true },    // New
        { data: 'actions', orderable:false, searchable:false }
    ],
    rowCallback: function(row, data){
        $(row).css('cursor','pointer');
        $(row).on('click', function(){
            window.location = '/assignments/' + data.id;
        });
    }
});


</script>
@endpush
