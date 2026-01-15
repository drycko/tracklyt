@extends('layouts.app')

@section('title', 'Edit Maintenance Report')
@section('header', 'Edit Report: ' . $maintenanceReport->report_number)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-reports.index') }}">Maintenance Reports</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-reports.show', $maintenanceReport) }}">{{ $maintenanceReport->report_number }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<form action="{{ route('maintenance-reports.update', $maintenanceReport) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Report Details</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> You can only edit basic details. Tasks are loaded from the report type template and cannot be changed.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Report Number</label>
                        <input type="text" class="form-control" value="{{ $maintenanceReport->report_number }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Report Type</label>
                        <input type="text" class="form-control" value="{{ $maintenanceReport->reportType->name }}" disabled>
                        <small class="text-muted">Report type cannot be changed after creation</small>
                    </div>

                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror" 
                                id="project_id" name="project_id" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" 
                                    {{ (old('project_id', $maintenanceReport->project_id) == $project->id) ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assigned To</label>
                        <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                id="assigned_to" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach(\App\Models\User::where('tenant_id', auth()->user()->tenant_id)->get() as $user)
                            <option value="{{ $user->id }}" 
                                    {{ (old('assigned_to', $maintenanceReport->assigned_to) == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="scheduled_date" class="form-label">Scheduled Date</label>
                        <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                               id="scheduled_date" name="scheduled_date" 
                               value="{{ old('scheduled_date', $maintenanceReport->scheduled_date?->format('Y-m-d')) }}">
                        @error('scheduled_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $maintenanceReport->notes) }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Report Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Current Status</small>
                        <div>
                            @if($maintenanceReport->status === 'draft')
                            <span class="badge bg-secondary">Draft</span>
                            @elseif($maintenanceReport->status === 'in_progress')
                            <span class="badge bg-warning">In Progress</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Progress</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $maintenanceReport->completion_percentage }}%">
                                {{ $maintenanceReport->completion_percentage }}%
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Tasks</small>
                        <div>{{ $maintenanceReport->tasks->where('is_completed', true)->count() }} of {{ $maintenanceReport->tasks->count() }} completed</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Report
                        </button>
                        <a href="{{ route('maintenance-reports.show', $maintenanceReport) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Cancel
                        </a>
                        @if($maintenanceReport->status === 'draft')
                        <hr>
                        <form action="{{ route('maintenance-reports.destroy', $maintenanceReport) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this report?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Delete Report
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
