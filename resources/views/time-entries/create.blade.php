@extends('layouts.app')

@section('title', 'Log Time Entry')
@section('header', 'Log Time Entry')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('time-entries.index') }}">Time Entries</a></li>
<li class="breadcrumb-item active">Log Time</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('time-entries.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                            <option value="">Select Project</option>
                            @foreach(\App\Models\Project::active()->get() as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="task_id" class="form-label">Task <span class="text-danger">*</span></label>
                        <select class="form-select @error('task_id') is-invalid @enderror" id="task_id" name="task_id" required>
                            <option value="">Select Task</option>
                            @foreach(\App\Models\Task::all() as $task)
                            <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                {{ $task->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('task_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                   id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                            @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                   id="end_time" name="end_time" value="{{ old('end_time', '17:00') }}" required>
                            @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="duration_hours" class="form-label">Duration (Hours)</label>
                            <input type="number" class="form-control @error('duration_hours') is-invalid @enderror" 
                                   id="duration_hours" name="duration_hours" value="{{ old('duration_hours') }}" 
                                   step="0.25" min="0.25" max="24">
                            <small class="text-muted">Leave blank to auto-calculate from start/end time</small>
                            @error('duration_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="is_billable" class="form-label">Billing Status</label>
                            <select class="form-select @error('is_billable') is-invalid @enderror" id="is_billable" name="is_billable">
                                <option value="1" {{ old('is_billable', '1') == '1' ? 'selected' : '' }}>Billable</option>
                                <option value="0" {{ old('is_billable') == '0' ? 'selected' : '' }}>Non-Billable</option>
                            </select>
                            @error('is_billable')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                        <small class="text-muted">Describe the work you completed</small>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('time-entries.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Log Time
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Time Entry Tips -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-lightbulb text-warning me-2"></i>Quick Tips</h6>
                <ul class="mb-0 small text-muted">
                    <li>Duration can be manually entered or auto-calculated from start/end times</li>
                    <li>Mark entries as billable to include them in client invoices</li>
                    <li>Add detailed notes to help with invoicing and project tracking</li>
                    <li>Use the timer feature for real-time tracking instead of manual entry</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calculate duration when start/end times change
document.addEventListener('DOMContentLoaded', function() {
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const durationHours = document.getElementById('duration_hours');

    function calculateDuration() {
        if (startTime.value && endTime.value) {
            const start = new Date('2000-01-01 ' + startTime.value);
            const end = new Date('2000-01-01 ' + endTime.value);
            const diff = (end - start) / 1000 / 60 / 60; // Convert to hours
            if (diff > 0) {
                durationHours.value = diff.toFixed(2);
            }
        }
    }

    startTime.addEventListener('change', calculateDuration);
    endTime.addEventListener('change', calculateDuration);
});
</script>
@endsection
