@extends('layouts.app')

@section('title', 'Create Maintenance Report')
@section('header', 'Create Maintenance Report')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-reports.index') }}">Maintenance Reports</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<form action="{{ route('maintenance-reports.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Report Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror" 
                                id="project_id" name="project_id" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_report_type_id" class="form-label">
                            Report Type <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('maintenance_report_type_id') is-invalid @enderror" 
                                id="maintenance_report_type_id" name="maintenance_report_type_id" required>
                            <option value="">Select Report Type</option>
                            @foreach($types as $type)
                            <option value="{{ $type->id }}" 
                                    data-task-count="{{ $type->taskTemplates->count() }}"
                                    {{ old('maintenance_report_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->taskTemplates->count() }} tasks)
                            </option>
                            @endforeach
                        </select>
                        @error('maintenance_report_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" id="typeDescription"></div>
                    </div>

                    <div class="mb-3">
                        <label for="scheduled_date" class="form-label">Scheduled Date (Optional)</label>
                        <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                               id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}">
                        @error('scheduled_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Task Preview -->
            <div class="card border-0 shadow-sm" id="taskPreview" style="display: none;">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Task Preview</h5>
                </div>
                <div class="card-body">
                    <div id="taskList"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                id="assigned_to" name="assigned_to">
                            <option value="">Assign to me</option>
                            @foreach(\App\Models\User::where('tenant_id', auth()->user()->tenant_id)->get() as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create Report
                        </button>
                        <a href="{{ route('maintenance-reports.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
const typeData = {!! json_encode($types->keyBy('id')->map(function($type) {
    return [
        'name' => $type->name,
        'description' => $type->description,
        'tasks' => $type->taskTemplates->map(function($task) {
            return [
                'name' => $task->task_name,
                'description' => $task->task_description,
                'estimated_time_minutes' => $task->estimated_time_minutes
            ];
        })
    ];
})) !!};

document.getElementById('maintenance_report_type_id').addEventListener('change', function() {
    const typeId = this.value;
    const taskPreview = document.getElementById('taskPreview');
    const taskList = document.getElementById('taskList');
    const typeDescription = document.getElementById('typeDescription');
    
    if (!typeId || !typeData[typeId]) {
        taskPreview.style.display = 'none';
        typeDescription.textContent = '';
        return;
    }
    
    const type = typeData[typeId];
    
    // Show description
    if (type.description) {
        typeDescription.textContent = type.description;
    }
    
    // Show task preview
    taskPreview.style.display = 'block';
    
    let html = '<div class="list-group list-group-flush">';
    type.tasks.forEach((task, index) => {
        html += `
            <div class="list-group-item px-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1 text-muted">${index + 1}. ${task.name}</h6>
                        ${task.description ? `<p class="mb-1 text-muted small">${task.description}</p>` : ''}
                        ${task.estimated_time_minutes ? `<small class="text-muted"><i class="bi bi-clock me-1"></i>${task.estimated_time_minutes} min</small>` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    taskList.innerHTML = html;
});

// Trigger on page load if type is selected
if (document.getElementById('maintenance_report_type_id').value) {
    document.getElementById('maintenance_report_type_id').dispatchEvent(new Event('change'));
}
</script>
@endsection
