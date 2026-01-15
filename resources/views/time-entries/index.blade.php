@extends('layouts.app')

@section('title', 'Time Entries')
@section('header', 'Time Entries')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Time Entries</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#startTimerModal">
        <i class="bi bi-play-circle me-2"></i>Start Timer
    </button>
    <a href="{{ route('time-entries.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Log Time
    </a>
</div>
@endsection

@section('content')
<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('time-entries.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">From Date</label>
                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">To Date</label>
                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label for="billable" class="form-label">Billable</label>
                <select class="form-select form-select-sm" id="billable" name="billable">
                    <option value="">All</option>
                    <option value="yes" {{ request('billable') === 'yes' ? 'selected' : '' }}>Billable</option>
                    <option value="no" {{ request('billable') === 'no' ? 'selected' : '' }}>Non-Billable</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="locked" class="form-label">Status</label>
                <select class="form-select form-select-sm" id="locked" name="locked">
                    <option value="">All</option>
                    <option value="no" {{ request('locked') === 'no' ? 'selected' : '' }}>Unlocked</option>
                    <option value="yes" {{ request('locked') === 'yes' ? 'selected' : '' }}>Locked</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

@if($timeEntries->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Project</th>
                        <th>Task</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeEntries as $entry)
                    <tr class="{{ $entry->isRunning() ? 'table-info' : '' }}">
                        <td>
                            <div class="fw-semibold">{{ $entry->start_time->format('M d, Y') }}</div>
                            <small class="text-muted">
                                {{ $entry->start_time->format('g:i A') }} 
                                @if($entry->end_time)
                                - {{ $entry->end_time->format('g:i A') }}
                                @else
                                <span class="badge bg-primary">Running</span>
                                @endif
                            </small>
                        </td>
                        <td>{{ $entry->user->name }}</td>
                        <td>
                            <a href="{{ route('projects.show', $entry->project) }}" class="text-decoration-none">
                                {{ $entry->project->name }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('tasks.show', $entry->task) }}" class="text-decoration-none">
                                {{ $entry->task->name }}
                            </a>
                        </td>
                        <td>
                            <strong>{{ number_format($entry->duration_hours, 2) }} hrs</strong>
                            @if($entry->is_billable)
                            <i class="bi bi-currency-dollar text-success" title="Billable"></i>
                            @endif
                        </td>
                        <td>
                            @if($entry->isLocked())
                            <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i> Locked</span>
                            @elseif($entry->isRunning())
                            <span class="badge bg-primary"><i class="bi bi-play-fill"></i> Running</span>
                            @else
                            <span class="badge bg-success"><i class="bi bi-check-lg"></i> Completed</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                @if($entry->isRunning() && $entry->user_id === auth()->id())
                                <form action="{{ route('time-entries.stop', $entry) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger" title="Stop Timer">
                                        <i class="bi bi-stop-circle"></i>
                                    </button>
                                </form>
                                @endif
                                @if(!$entry->isLocked())
                                <a href="{{ route('time-entries.edit', $entry) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                <a href="{{ route('time-entries.show', $entry) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total Hours:</th>
                        <th colspan="3">
                            <strong class="text-primary">{{ number_format($timeEntries->sum('duration_hours'), 2) }} hrs</strong>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top">
        {{ $timeEntries->links() }}
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-clock-history display-1 text-muted"></i>
        <h4 class="mt-3">No Time Entries Found</h4>
        <p class="text-muted">Start tracking your time or adjust your filters.</p>
        <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#startTimerModal">
                <i class="bi bi-play-circle me-2"></i>Start Timer
            </button>
            <a href="{{ route('time-entries.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Log Time
            </a>
        </div>
    </div>
</div>
@endif

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Today</small>
                        <h3 class="mb-0">{{ number_format(\App\Models\TimeEntry::whereDate('start_time', today())->sum('duration_minutes') / 60, 1) }}h</h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-calendar-day display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">This Week</small>
                        <h3 class="mb-0">{{ number_format(\App\Models\TimeEntry::whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->sum('duration_minutes') / 60, 1) }}h</h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-calendar-week display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Billable</small>
                        <h3 class="mb-0">{{ number_format(\App\Models\TimeEntry::billable()->sum('duration_minutes') / 60, 0) }}h</h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-currency-dollar display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Locked</small>
                        <h3 class="mb-0">{{ \App\Models\TimeEntry::locked()->count() }}</h3>
                    </div>
                    <div class="text-secondary">
                        <i class="bi bi-lock-fill display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start Timer Modal -->
<div class="modal fade" id="startTimerModal" tabindex="-1" aria-labelledby="startTimerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('time-entries.start') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="startTimerModalLabel">Start Timer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="timer_project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select" id="timer_project_id" name="project_id" required>
                            <option value="">Select Project</option>
                            @foreach(\App\Models\Project::active()->get() as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="timer_task_id" class="form-label">Task <span class="text-danger">*</span></label>
                        <select class="form-select" id="timer_task_id" name="task_id" required>
                            <option value="">Select Task</option>
                            @foreach(\App\Models\Task::pending()->get() as $task)
                            <option value="{{ $task->id }}">{{ $task->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="timer_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="timer_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-play-circle me-1"></i>Start Timer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
