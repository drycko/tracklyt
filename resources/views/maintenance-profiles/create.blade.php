@extends('layouts.app')

@section('title', 'Create Maintenance Profile')
@section('header', 'Create Maintenance Profile')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-profiles.index') }}">Maintenance Profiles</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Profile Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('maintenance-profiles.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror" 
                                id="project_id" name="project_id" required>
                            <option value="">Select a project...</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }} ({{ $project->client->name }})
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Only projects without an existing maintenance profile are shown.</small>
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_type" class="form-label">Maintenance Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('maintenance_type') is-invalid @enderror" 
                                id="maintenance_type" name="maintenance_type" required>
                            <option value="retainer" {{ old('maintenance_type', 'retainer') == 'retainer' ? 'selected' : '' }}>
                                Retainer (Fixed monthly hours)
                            </option>
                            <option value="hourly" {{ old('maintenance_type') == 'hourly' ? 'selected' : '' }}>
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
                               value="{{ old('monthly_hours') }}"
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
                               value="{{ old('rate') }}"
                               step="0.01" 
                               min="0"
                               required
                               placeholder="e.g., 500.00">
                        @error('rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Rate per hour for this maintenance work.</small>
                    </div>

                    <div class="mb-3">
                        <label for="rollover_hours" class="form-label">Initial Rollover Hours</label>
                        <input type="number" 
                               class="form-control @error('rollover_hours') is-invalid @enderror" 
                               id="rollover_hours" 
                               name="rollover_hours" 
                               value="{{ old('rollover_hours', 0) }}"
                               step="0.5" 
                               min="0"
                               placeholder="0">
                        @error('rollover_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Unused hours from previous period (if migrating from another system).</small>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('start_date') is-invalid @enderror" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', now()->format('Y-m-d')) }}"
                               required>
                        @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">When the maintenance agreement begins.</small>
                    </div>

                    <div class="mb-3">
                        <label for="sla_notes" class="form-label">SLA Notes</label>
                        <textarea class="form-control @error('sla_notes') is-invalid @enderror" 
                                  id="sla_notes" 
                                  name="sla_notes" 
                                  rows="4"
                                  placeholder="Response time, coverage hours, exclusions, etc.">{{ old('sla_notes') }}</textarea>
                        @error('sla_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Service level agreement terms and conditions.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Create Profile
                        </button>
                        <a href="{{ route('maintenance-profiles.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">Help</h6>
            </div>
            <div class="card-body">
                <h6>Maintenance Types</h6>
                <p class="small text-muted">
                    <strong>Retainer:</strong> Fixed monthly hours with rollover tracking. Unused hours carry over to next month.
                </p>
                <p class="small text-muted">
                    <strong>Hourly:</strong> No monthly limit, bill all hours worked at the specified rate.
                </p>
                
                <hr>
                
                <h6>Rollover Hours</h6>
                <p class="small text-muted">
                    Automatically calculated each month. Any unused retainer hours are added to next month's allocation.
                </p>
            </div>
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
        monthlyHoursInput.value = '';
    }
});

// Trigger on page load
document.getElementById('maintenance_type').dispatchEvent(new Event('change'));
</script>
@endsection
