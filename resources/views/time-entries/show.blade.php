@extends('layouts.app')

@section('title', 'Time Entry Details')
@section('header', 'Time Entry Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('time-entries.index') }}">Time Entries</a></li>
<li class="breadcrumb-item active">Details</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    @if(!$timeEntry->isLocked())
    <a href="{{ route('time-entries.edit', $timeEntry) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-2"></i>Edit
    </a>
    @endif
    
    @if(auth()->user()->is_admin || auth()->user()->is_owner)
        @if($timeEntry->isLocked())
        <form action="{{ route('time-entries.unlock', $timeEntry) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-unlock me-2"></i>Unlock
            </button>
        </form>
        @else
        <form action="{{ route('time-entries.lock', $timeEntry) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <i class="bi bi-lock me-2"></i>Lock
            </button>
        </form>
        @endif
    @endif
    
    @if(!$timeEntry->isLocked() && $timeEntry->user_id === auth()->id())
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
        <i class="bi bi-trash me-2"></i>Delete
    </button>
    @endif
</div>
@endsection

@section('content')
<div class="row">
    <!-- Main Entry Details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    Entry Information
                    @if($timeEntry->isRunning())
                    <span class="badge bg-primary ms-2"><i class="bi bi-play-fill"></i> Running</span>
                    @elseif($timeEntry->isLocked())
                    <span class="badge bg-warning text-dark ms-2"><i class="bi bi-lock-fill"></i> Locked</span>
                    @else
                    <span class="badge bg-success ms-2"><i class="bi bi-check-lg"></i> Completed</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted small">User</label>
                        <div class="fw-semibold">{{ $timeEntry->user->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Project</label>
                        <div>
                            <a href="{{ route('projects.show', $timeEntry->project) }}" class="text-decoration-none fw-semibold">
                                {{ $timeEntry->project->name }}
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Task</label>
                        <div>
                            <a href="{{ route('tasks.show', $timeEntry->task) }}" class="text-decoration-none fw-semibold">
                                {{ $timeEntry->task->name }}
                            </a>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted small">Date</label>
                        <div class="fw-semibold">{{ $timeEntry->start_time->format('M d, Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Start Time</label>
                        <div class="fw-semibold">{{ $timeEntry->start_time->format('g:i A') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">End Time</label>
                        <div class="fw-semibold">
                            @if($timeEntry->end_time)
                            {{ $timeEntry->end_time->format('g:i A') }}
                            @else
                            <span class="badge bg-primary">Running</span>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="text-muted small">Duration</label>
                        <div class="fw-semibold text-primary fs-4">{{ number_format($timeEntry->duration_hours, 2) }} hours</div>
                        <small class="text-muted">{{ $timeEntry->duration_minutes }} minutes</small>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Billing Status</label>
                        <div>
                            @if($timeEntry->is_billable)
                            <span class="badge bg-success"><i class="bi bi-currency-dollar"></i> Billable</span>
                            @else
                            <span class="badge bg-secondary">Non-Billable</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Lock Status</label>
                        <div>
                            @if($timeEntry->isLocked())
                            <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i> Locked</span>
                            <div class="small text-muted mt-1">{{ $timeEntry->locked_at->format('M d, Y g:i A') }}</div>
                            @else
                            <span class="badge bg-secondary"><i class="bi bi-unlock"></i> Unlocked</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($timeEntry->notes)
                <hr>
                <div class="mb-0">
                    <label class="text-muted small">Notes</label>
                    <div class="p-3 bg-light rounded">{{ $timeEntry->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Activity Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-badge bg-success">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="fw-semibold">Entry Created</div>
                            <div class="text-muted small">{{ $timeEntry->created_at->format('M d, Y g:i A') }}</div>
                            <div class="small">by {{ $timeEntry->user->name }}</div>
                        </div>
                    </div>
                    
                    @if($timeEntry->created_at != $timeEntry->updated_at)
                    <div class="timeline-item">
                        <div class="timeline-badge bg-primary">
                            <i class="bi bi-pencil"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="fw-semibold">Entry Updated</div>
                            <div class="text-muted small">{{ $timeEntry->updated_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($timeEntry->isLocked())
                    <div class="timeline-item">
                        <div class="timeline-badge bg-warning">
                            <i class="bi bi-lock"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="fw-semibold">Entry Locked</div>
                            <div class="text-muted small">{{ $timeEntry->locked_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($timeEntry->end_time)
                    <div class="timeline-item">
                        <div class="timeline-badge bg-success">
                            <i class="bi bi-stop-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="fw-semibold">Timer Stopped</div>
                            <div class="text-muted small">{{ $timeEntry->end_time->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="list-group list-group-flush">
                @if(!$timeEntry->isLocked())
                <a href="{{ route('time-entries.edit', $timeEntry) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-pencil me-2"></i>Edit Entry
                </a>
                @endif
                <a href="{{ route('projects.show', $timeEntry->project) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-folder me-2"></i>View Project
                </a>
                <a href="{{ route('tasks.show', $timeEntry->task) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-check-square me-2"></i>View Task
                </a>
            </div>
        </div>

        <!-- Project Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Project Time Summary</h6>
            </div>
            <div class="card-body">
                @php
                $projectTime = \App\Models\TimeEntry::where('project_id', $timeEntry->project_id)->sum('duration_minutes');
                $taskTime = \App\Models\TimeEntry::where('task_id', $timeEntry->task_id)->sum('duration_minutes');
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Total Project Time</small>
                        <span class="fw-semibold">{{ number_format($projectTime / 60, 1) }}h</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Task Time</small>
                        <span class="fw-semibold">{{ number_format($taskTime / 60, 1) }}h</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $projectTime > 0 ? ($taskTime / $projectTime) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Related Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Client</label>
                    <div class="fw-semibold">{{ $timeEntry->project->client->name }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Task Status</label>
                    <div>
                        @if($timeEntry->task->isCompleted())
                        <span class="badge bg-success">Completed</span>
                        @else
                        <span class="badge bg-primary">In Progress</span>
                        @endif
                    </div>
                </div>
                @if($timeEntry->task->due_date)
                <div class="mb-0">
                    <label class="text-muted small">Task Due Date</label>
                    <div class="fw-semibold">{{ $timeEntry->task->due_date->format('M d, Y') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this time entry? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('time-entries.destroy', $timeEntry) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-badge {
    position: absolute;
    left: -30px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 10px;
}
.timeline-content {
    padding-left: 10px;
}
</style>
@endsection
