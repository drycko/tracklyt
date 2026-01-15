@extends('layouts.app')

@section('title', 'Tasks')
@section('header', 'Tasks')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Tasks</li>
@endsection

@section('actions')
<a href="{{ route('tasks.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>New Task
</a>
@endsection

@section('content')
<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
            All Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'todo' ? 'active' : '' }}" href="{{ route('tasks.index', ['status' => 'todo']) }}">
            <span class="badge bg-secondary">{{ \App\Models\Task::where('status', 'todo')->count() }}</span> To Do
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'in_progress' ? 'active' : '' }}" href="{{ route('tasks.index', ['status' => 'in_progress']) }}">
            <span class="badge bg-primary">{{ \App\Models\Task::where('status', 'in_progress')->count() }}</span> In Progress
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('tasks.index', ['status' => 'completed']) }}">
            <span class="badge bg-success">{{ \App\Models\Task::where('status', 'completed')->count() }}</span> Completed
        </a>
    </li>
</ul>

@if($tasks->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Task</th>
                        <th>Project</th>
                        <th>Assigned To</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Hours</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td>
                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-semibold">
                                {{ $task->name }}
                            </a>
                            @if($task->is_billable)
                            <i class="bi bi-currency-dollar text-success" title="Billable"></i>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                {{ $task->project->name }}
                            </a>
                        </td>
                        <td>
                            @if($task->assignedUser)
                            {{ $task->assignedUser->name }}
                            @else
                            <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            @if($task->priority === 'high')
                            <span class="badge bg-danger">High</span>
                            @elseif($task->priority === 'medium')
                            <span class="badge bg-warning text-dark">Medium</span>
                            @else
                            <span class="badge bg-secondary">Low</span>
                            @endif
                        </td>
                        <td>
                            @if($task->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                            @elseif($task->status === 'in_progress')
                            <span class="badge bg-primary">In Progress</span>
                            @else
                            <span class="badge bg-secondary">To Do</span>
                            @endif
                        </td>
                        <td>
                            @if($task->due_date)
                            {{ $task->due_date->format('M d, Y') }}
                            @if($task->due_date->isPast() && $task->status !== 'completed')
                            <i class="bi bi-exclamation-circle text-danger" title="Overdue"></i>
                            @endif
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($task->total_hours > 0)
                            {{ number_format($task->total_hours, 1) }}h
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                @if($task->status !== 'completed')
                                <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Complete">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('tasks.reopen', $task) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning" title="Reopen">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top">
        {{ $tasks->links() }}
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-list-task display-1 text-muted"></i>
        <h4 class="mt-3">No Tasks Found</h4>
        <p class="text-muted">Get started by creating your first task.</p>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Task
        </a>
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
                        <small class="text-muted d-block">To Do</small>
                        <h3 class="mb-0">{{ \App\Models\Task::where('status', 'todo')->count() }}</h3>
                    </div>
                    <div class="text-secondary">
                        <i class="bi bi-list-check display-6"></i>
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
                        <small class="text-muted d-block">In Progress</small>
                        <h3 class="mb-0">{{ \App\Models\Task::where('status', 'in_progress')->count() }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-hourglass-split display-6"></i>
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
                        <small class="text-muted d-block">Completed</small>
                        <h3 class="mb-0">{{ \App\Models\Task::where('status', 'completed')->count() }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-check-circle display-6"></i>
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
                        <small class="text-muted d-block">Overdue</small>
                        <h3 class="mb-0 text-danger">
                            {{ \App\Models\Task::whereDate('due_date', '<', now())->where('status', '!=', 'completed')->count() }}
                        </h3>
                    </div>
                    <div class="text-danger">
                        <i class="bi bi-exclamation-triangle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
