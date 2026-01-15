@extends('layouts.app')

@section('title', $task->name)
@section('header', $task->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
<li class="breadcrumb-item active">{{ $task->name }}</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    @if(!$task->isCompleted())
    <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-2"></i>Mark Complete
        </button>
    </form>
    @else
    <form action="{{ route('tasks.reopen', $task) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Reopen
        </button>
    </form>
    @endif

    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">
        <i class="bi bi-pencil me-2"></i>Edit
    </a>

    <div class="dropdown">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#"><i class="bi bi-clock-history me-2"></i>Log Time</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" 
                   onclick="if(confirm('Are you sure?')) { document.getElementById('delete-form').submit(); }">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
            <form id="delete-form" action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Task Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Task Details</h5>
                <div>
                    @if($task->status === 'completed')
                    <span class="badge bg-success">Completed</span>
                    @elseif($task->status === 'in_progress')
                    <span class="badge bg-primary">In Progress</span>
                    @else
                    <span class="badge bg-secondary">To Do</span>
                    @endif
                    
                    @if($task->priority === 'high')
                    <span class="badge bg-danger">High Priority</span>
                    @elseif($task->priority === 'medium')
                    <span class="badge bg-warning text-dark">Medium Priority</span>
                    @else
                    <span class="badge bg-secondary">Low Priority</span>
                    @endif

                    @if($task->is_billable)
                    <span class="badge bg-success"><i class="bi bi-currency-dollar"></i> Billable</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Project</small>
                        <a href="{{ route('projects.show', $task->project) }}" class="fw-semibold text-decoration-none">
                            {{ $task->project->name }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Assigned To</small>
                        <span class="fw-semibold">
                            @if($task->assignedUser)
                            {{ $task->assignedUser->name }}
                            @else
                            <span class="text-muted">Unassigned</span>
                            @endif
                        </span>
                    </div>
                </div>

                @if($task->description)
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <div class="bg-light rounded p-3 markdown-content">
                        {!! markdown(e($task->description)) !!}
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Due Date</small>
                        <div class="fs-6 fw-semibold">
                            @if($task->due_date)
                            {{ $task->due_date->format('M d, Y') }}
                            @if($task->due_date->isPast() && !$task->isCompleted())
                            <span class="badge bg-danger ms-2">Overdue</span>
                            @elseif($task->due_date->diffInDays(now()) <= 3 && !$task->isCompleted())
                            <span class="badge bg-warning ms-2">Due Soon</span>
                            @endif
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Created</small>
                        <div class="fs-6">{{ $task->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Last Updated</small>
                        <div class="fs-6">{{ $task->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Entries -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Time Entries</h5>
                <a href="{{ route('time-entries.create', ['task_id' => $task->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus me-1"></i>Log Time
                </a>
            </div>
            <div class="card-body">
                @if($task->timeEntries->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($task->timeEntries as $entry)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">{{ $entry->created_at->format('M d, Y') }} - {{ $entry->user->name }}</small>
                                    <strong>{{ number_format($entry->hours, 2) }} hrs</strong>
                                </div>
                                @if($entry->description)
                                <small class="text-muted d-block mt-1">{{ $entry->description }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total Time:</strong>
                        <h4 class="mb-0 text-primary">{{ number_format($task->total_hours, 2) }} hours</h4>
                    </div>
                </div>
                @else
                <p class="text-muted mb-0 text-center py-3">No time entries yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Task Stats</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Total Time Logged</small>
                    <h4 class="mb-0">{{ number_format($task->total_hours, 1) }} hrs</h4>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted d-block">Time Entries</small>
                    <h4 class="mb-0">{{ $task->timeEntries->count() }}</h4>
                </div>

                @if($task->due_date && !$task->isCompleted())
                <hr>
                <div class="mb-0">
                    <small class="text-muted d-block">Time Remaining</small>
                    <h4 class="mb-0 {{ $task->due_date->isPast() ? 'text-danger' : 'text-success' }}">
                        @if($task->due_date->isPast())
                        {{ $task->due_date->diffForHumans() }}
                        @else
                        {{ $task->due_date->diffForHumans() }}
                        @endif
                    </h4>
                </div>
                @endif
            </div>
        </div>

        <!-- Task Activity -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Activity</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="d-flex mb-3">
                        <div class="text-muted small" style="min-width: 80px;">Created</div>
                        <div class="ms-3">
                            <strong>{{ $task->created_at->format('M d, Y') }}</strong>
                            <small class="text-muted d-block">{{ $task->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    @if($task->timeEntries->count() > 0)
                    <div class="d-flex mb-3">
                        <div class="text-muted small" style="min-width: 80px;">First Entry</div>
                        <div class="ms-3">
                            <strong>{{ $task->timeEntries->first()->created_at->format('M d, Y') }}</strong>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <div class="text-muted small" style="min-width: 80px;">Last Entry</div>
                        <div class="ms-3">
                            <strong>{{ $task->timeEntries->last()->created_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                    @endif

                    @if($task->isCompleted())
                    <div class="d-flex mb-3">
                        <div class="text-muted small" style="min-width: 80px;">Completed</div>
                        <div class="ms-3">
                            <strong class="text-success">{{ $task->updated_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
