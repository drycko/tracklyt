@extends('layouts.app')

@section('title', 'Maintenance Report: ' . $maintenanceReport->report_number)
@section('header', $maintenanceReport->report_number)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-reports.index') }}">Maintenance Reports</a></li>
<li class="breadcrumb-item active">{{ $maintenanceReport->report_number }}</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    <a href="{{ route('maintenance-reports.pdf', $maintenanceReport) }}" class="btn btn-outline-primary">
        <i class="bi bi-download me-2"></i>Download PDF
    </a>
    
    @if($maintenanceReport->status === 'draft')
    <a href="{{ route('maintenance-reports.start', $maintenanceReport) }}" class="btn btn-success">
        <i class="bi bi-play-circle me-2"></i>Start Report
    </a>
    @endif
    
    @if($maintenanceReport->status === 'in_progress')
    <form action="{{ route('maintenance-reports.complete', $maintenanceReport) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success" 
                {{ $maintenanceReport->completion_percentage < 100 ? 'disabled title=Complete all tasks first' : '' }}>
            <i class="bi bi-check-circle me-2"></i>Mark Complete
        </button>
    </form>
    @endif
    
    @if(in_array($maintenanceReport->status, ['draft', 'in_progress']))
    <a href="{{ route('maintenance-reports.edit', $maintenanceReport) }}" class="btn btn-outline-secondary">
        <i class="bi bi-pencil me-2"></i>Edit
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Report Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Project</small>
                        <a href="{{ route('projects.show', $maintenanceReport->project) }}" class="text-decoration-none fw-semibold">
                            {{ $maintenanceReport->project->name }}
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Report Type</small>
                        <div class="fw-semibold">{{ $maintenanceReport->reportType->name }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Assigned To</small>
                        <div>{{ $maintenanceReport->assignedTo->name ?? 'Unassigned' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Status</small>
                        <div>
                            @if($maintenanceReport->status === 'draft')
                            <span class="badge bg-secondary">Draft</span>
                            @elseif($maintenanceReport->status === 'in_progress')
                            <span class="badge bg-warning">In Progress</span>
                            @elseif($maintenanceReport->status === 'completed')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
                            @endif
                        </div>
                    </div>
                    @if($maintenanceReport->scheduled_date)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Scheduled Date</small>
                        <div>{{ $maintenanceReport->scheduled_date->format('M d, Y') }}</div>
                    </div>
                    @endif
                    @if($maintenanceReport->started_at)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Started At</small>
                        <div>{{ $maintenanceReport->started_at->format('M d, Y g:i A') }}</div>
                    </div>
                    @endif
                    @if($maintenanceReport->completed_at)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Completed At</small>
                        <div>{{ $maintenanceReport->completed_at->format('M d, Y g:i A') }}</div>
                    </div>
                    @endif
                    @if($maintenanceReport->notes)
                    <div class="col-12">
                        <small class="text-muted d-block">Notes</small>
                        <div>{{ $maintenanceReport->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Overall Progress</h6>
                    <span class="badge bg-primary">{{ $maintenanceReport->completion_percentage }}%</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $maintenanceReport->completion_percentage }}%" 
                         aria-valuenow="{{ $maintenanceReport->completion_percentage }}" 
                         aria-valuemin="0" aria-valuemax="100">
                        {{ $maintenanceReport->completion_percentage }}%
                    </div>
                </div>
                <div class="mt-2 text-muted small">
                    {{ $maintenanceReport->tasks->where('is_completed', true)->count() }} of {{ $maintenanceReport->tasks->count() }} tasks completed
                </div>
            </div>
        </div>

        <!-- Tasks -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tasks</h5>
            </div>
            <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                @foreach($maintenanceReport->tasks as $task)
                <div class="card mb-3 {{ $task->is_completed ? 'border-success' : '' }}">
                    <div class="card-header {{ $task->is_completed ? 'bg-success bg-opacity-10' : 'bg-light' }}" 
                         style="cursor: pointer;"
                         data-bs-toggle="collapse" 
                         data-bs-target="#taskCollapse-{{ $task->id }}"
                         aria-expanded="{{ (!$task->is_completed && ($task->comments || $task->screenshots)) ? 'true' : 'false' }}"
                         aria-controls="taskCollapse-{{ $task->id }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start w-100">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="task-{{ $task->id }}" 
                                           {{ $task->is_completed ? 'checked' : '' }}
                                           {{ $maintenanceReport->status === 'completed' ? 'disabled' : '' }}
                                           onclick="event.stopPropagation();"
                                           onchange="toggleTaskCompletion(event, {{ $task->id }}, this.checked)">
                                    <label class="form-check-label fw-semibold {{ $task->is_completed ? 'text-decoration-line-through' : '' }}" 
                                           for="task-{{ $task->id }}"
                                           onclick="event.stopPropagation();">
                                        {{ $task->task_name }}
                                    </label>
                                </div>
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </div>
                            @if($task->is_completed)
                            <span class="badge bg-success ms-2"><i class="bi bi-check-circle me-1"></i>Complete</span>
                            @endif
                        </div>
                        @if($task->task_description)
                        <p class="mb-0 mt-2 text-muted small" style="padding-left: 2rem;">{{ $task->task_description }}</p>
                        @endif
                        @if($task->estimated_time_minutes || $task->time_spent_minutes)
                        <small class="text-muted" style="padding-left: 2rem;">
                            <i class="bi bi-clock me-1"></i>
                            @if($task->estimated_time_minutes)
                                Est. {{ $task->estimated_time_minutes }} min
                            @endif
                            @if($task->time_spent_minutes)
                                @if($task->estimated_time_minutes) | @endif
                                <span class="fw-semibold">Actual: {{ $task->time_spent_minutes }} min</span>
                            @endif
                        </small>
                        @endif
                    </div>
                    
                    <div class="collapse {{ (!$task->is_completed && ($task->comments || $task->screenshots)) ? 'show' : '' }}" 
                         id="taskCollapse-{{ $task->id }}">
                        <div class="card-body">
                        <form action="{{ route('maintenance-reports.tasks.update', [$maintenanceReport, $task]) }}" 
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_completed" id="isCompleted-{{ $task->id }}" value="{{ $task->is_completed ? '1' : '0' }}">
                            
                            <div class="mb-3">
                                <label for="comments-{{ $task->id }}" class="form-label">Comments</label>
                                <textarea class="form-control" id="comments-{{ $task->id }}" name="comments" 
                                          rows="2" {{ $maintenanceReport->status === 'completed' ? 'readonly' : '' }}>{{ $task->comments }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="time_spent-{{ $task->id }}" class="form-label">Time Spent (minutes)</label>
                                <input type="number" class="form-control" id="time_spent-{{ $task->id }}" 
                                       name="time_spent_minutes" value="{{ $task->time_spent_minutes }}" min="0"
                                       {{ $maintenanceReport->status === 'completed' ? 'readonly' : '' }}>
                            </div>
                            
                            @if($maintenanceReport->status !== 'completed')
                            <div class="mb-3">
                                <label for="screenshots-{{ $task->id }}" class="form-label">Upload Screenshots</label>
                                <input type="file" class="form-control" id="screenshots-{{ $task->id }}" 
                                       name="screenshots[]" multiple accept="image/*">
                                <small class="text-muted">Max 5MB per image</small>
                            </div>
                            @endif
                            
                            @if($task->screenshots && count($task->screenshots) > 0)
                            <div class="mb-3">
                                <label class="form-label">Existing Screenshots</label>
                                <div class="row g-2">
                                    @foreach($task->screenshots as $index => $screenshot)
                                    <div class="col-md-3">
                                        <div class="position-relative">
                                            <img src="{{ Storage::url($screenshot) }}" 
                                                 class="img-thumbnail w-100" 
                                                 style="cursor: pointer;"
                                                 onclick="window.open('{{ Storage::url($screenshot) }}', '_blank')">
                                            @if($maintenanceReport->status !== 'completed')
                                            <form action="{{ route('maintenance-reports.tasks.screenshots.delete', [$maintenanceReport, $task, $index]) }}" 
                                                  method="POST" class="position-absolute top-0 end-0 m-1"
                                                  onsubmit="return confirm('Delete this screenshot?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            @if($maintenanceReport->status !== 'completed')
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save me-1"></i>Save
                            </button>
                            @endif
                        </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Report Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">Report Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Report Number</small>
                    <div class="fw-semibold">{{ $maintenanceReport->report_number }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <div>{{ $maintenanceReport->created_at->format('M d, Y g:i A') }}</div>
                    <small class="text-muted">by {{ $maintenanceReport->createdBy->name }}</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Tasks</small>
                    <div class="fw-semibold">{{ $maintenanceReport->tasks->count() }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Completed Tasks</small>
                    <div class="fw-semibold text-success">{{ $maintenanceReport->tasks->where('is_completed', true)->count() }}</div>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Total Time Spent</small>
                    <div class="fw-semibold">
                        {{ $maintenanceReport->tasks->sum('time_spent_minutes') }} minutes
                        ({{ number_format($maintenanceReport->tasks->sum('time_spent_minutes') / 60, 1) }} hours)
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        @if($maintenanceReport->status !== 'completed')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="list-group list-group-flush">
                @if($maintenanceReport->status === 'draft')
                <a href="{{ route('maintenance-reports.start', $maintenanceReport) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-play-circle text-success me-2"></i>Start Report
                </a>
                @endif
                <a href="{{ route('maintenance-reports.edit', $maintenanceReport) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-pencil me-2"></i>Edit Report
                </a>
                <a href="{{ route('projects.show', $maintenanceReport->project) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-folder me-2"></i>View Project
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleTaskCompletion(event, taskId, isChecked) {
    // Stop the event from bubbling to parent collapse trigger
    event.stopPropagation();
    
    const hiddenInput = document.getElementById('isCompleted-' + taskId);
    const collapseElement = document.getElementById('taskCollapse-' + taskId);
    const form = collapseElement.querySelector('form');
    
    hiddenInput.value = isChecked ? '1' : '0';
    
    // Auto-expand the collapse when checking a task
    if (isChecked && !collapseElement.classList.contains('show')) {
        const bsCollapse = new bootstrap.Collapse(collapseElement, {
            toggle: true
        });
        
        // Wait for collapse animation then submit
        collapseElement.addEventListener('shown.bs.collapse', function() {
            form.submit();
        }, { once: true });
    } else {
        // Submit immediately if already expanded or unchecking
        form.submit();
    }
}

// Add rotation effect to chevron icons
document.addEventListener('DOMContentLoaded', function() {
    const collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
    collapseElements.forEach(function(element) {
        const targetId = element.getAttribute('data-bs-target');
        const chevron = element.querySelector('.bi-chevron-down');
        
        if (targetId && chevron) {
            const target = document.querySelector(targetId);
            if (target) {
                target.addEventListener('show.bs.collapse', function() {
                    chevron.style.transform = 'rotate(180deg)';
                    chevron.style.transition = 'transform 0.3s';
                });
                target.addEventListener('hide.bs.collapse', function() {
                    chevron.style.transform = 'rotate(0deg)';
                    chevron.style.transition = 'transform 0.3s';
                });
                
                // Set initial state
                if (target.classList.contains('show')) {
                    chevron.style.transform = 'rotate(180deg)';
                }
            }
        }
    });
});
</script>
@endsection
