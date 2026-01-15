@extends('layouts.app')

@section('title', 'Edit Maintenance Profile')
@section('header', 'Edit Maintenance Profile')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-profiles.index') }}">Maintenance Profiles</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-profiles.show', $maintenanceProfile) }}">{{ $maintenanceProfile->project->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Edit Profile Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('maintenance-profiles.update', $maintenanceProfile) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror" 
                                id="project_id" name="project_id" required>
                            <option value="">Select a project...</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" 
                                    {{ old('project_id', $maintenanceProfile->project_id) == $project->id ? 'selected' : '' }}
                                    {{ $project->id != $maintenanceProfile->project_id && $project->maintenanceProfile ? 'disabled' : '' }}>
                                {{ $project->name }} ({{ $project->client->name }})
                                {{ $project->id != $maintenanceProfile->project_id && $project->maintenanceProfile ? ' - Has Profile' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_type" class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('maintenance_type') is-invalid @enderror" 
                                id="maintenance_type" name="maintenance_type" required>
                            <option value="retainer" {{ old('maintenance_type', $maintenanceProfile->maintenance_type) == 'retainer' ? 'selected' : '' }}>
                                Retainer (Fixed monthly hours)
                            </option>
                            <option value="hourly" {{ old('maintenance_type', $maintenanceProfile->maintenance_type) == 'hourly' ? 'selected' : '' }}>
                                Hourly (No monthly limit)
                            </option>
                        </select>
                        @error('maintenance_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="monthly_hours_field">
                        <label for="monthly_hours" class="form-label">Monthly Hours <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('monthly_hours') is-invalid @enderror" 
                               id="monthly_hours" 
                               name="monthly_hours" 
                               value="{{ old('monthly_hours', $maintenanceProfile->monthly_hours) }}"
                               step="0.5" 
                               min="0"
                               placeholder="e.g., 10">
                        @error('monthly_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Number of hours included in the monthly retainer.</small>
                    </div>

                    <div class="mb-3">
                        <label for="rate" class="form-label">Hourly Rate <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control @error('rate') is-invalid @enderror" 
                               id="rate" 
                               name="rate" 
                               value="{{ old('rate', $maintenanceProfile->rate) }}"
                               step="0.01" 
                               min="0"
                               required
                               placeholder="e.g., 500.00">
                        @error('rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="rollover_hours" class="form-label">Rollover Hours</label>
                        <input type="number" 
                               class="form-control @error('rollover_hours') is-invalid @enderror" 
                               id="rollover_hours" 
                               name="rollover_hours" 
                               value="{{ old('rollover_hours', $maintenanceProfile->rollover_hours) }}"
                               step="0.5" 
                               min="0"
                               placeholder="0">
                        @error('rollover_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Manually adjust rollover hours if needed.</small>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('start_date') is-invalid @enderror" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', $maintenanceProfile->start_date->format('Y-m-d')) }}"
                               required>
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sla_notes" class="form-label">SLA Notes</label>
                        <textarea class="form-control @error('sla_notes') is-invalid @enderror" 
                                  id="sla_notes" 
                                  name="sla_notes" 
                                  rows="4"
                                  placeholder="Response time, coverage hours, exclusions, etc.">{{ old('sla_notes', $maintenanceProfile->sla_notes) }}</textarea>
                        @error('sla_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Profile
                        </button>
                        <a href="{{ route('maintenance-profiles.show', $maintenanceProfile) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Current Usage</h6>
            </div>
            <div class="card-body">
                @if($maintenanceProfile->maintenance_type === 'retainer')
                <div class="text-center mb-3">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar {{ $maintenanceProfile->usage_percentage > 90 ? 'bg-danger' : ($maintenanceProfile->usage_percentage > 75 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" 
                             style="width: {{ min(100, $maintenanceProfile->usage_percentage) }}%">
                            {{ number_format($maintenanceProfile->usage_percentage, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        {{ number_format($maintenanceProfile->used_hours, 1) }} / {{ number_format($maintenanceProfile->total_available_hours, 1) }} hours used
                    </small>
                </div>
                @endif
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Hours Used:</span>
                    <span class="fw-semibold">{{ number_format($maintenanceProfile->used_hours, 1) }}</span>
                </div>
                @if($maintenanceProfile->maintenance_type === 'retainer')
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Remaining:</span>
                    <span class="fw-semibold {{ $maintenanceProfile->remaining_hours < 2 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($maintenanceProfile->remaining_hours, 1) }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Note:</strong> Changing the maintenance type or monthly hours will affect future calculations. Existing time entries are not modified.
        </div>
    </div>
</div>

<script>
document.getElementById('maintenance_type').addEventListener('change', function() {
    const monthlyHoursField = document.getElementById('monthly_hours_field');
    const monthlyHoursInput = document.getElementById('monthly_hours');
    
    if (this.value === 'retainer') {
        monthlyHoursField.style.display = 'block';
        monthlyHoursInput.required = true;
    } else {
        monthlyHoursField.style.display = 'none';
        monthlyHoursInput.required = false;
    }
});

// Trigger on page load
document.getElementById('maintenance_type').dispatchEvent(new Event('change'));
</script>
@endsection
