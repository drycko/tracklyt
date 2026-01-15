@extends('layouts.app')

@section('title', $maintenanceProfile->project->name . ' - Maintenance Profile')
@section('header', $maintenanceProfile->project->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('maintenance-profiles.index') }}">Maintenance Profiles</a></li>
<li class="breadcrumb-item active">{{ $maintenanceProfile->project->name }}</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    @if($maintenanceProfile->maintenance_type === 'retainer' && $maintenanceProfile->needsReset())
    <form action="{{ route('maintenance-profiles.reset', $maintenanceProfile) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-warning" onclick="return confirm('Reset this maintenance profile? Unused hours will rollover.')">
            <i class="bi bi-arrow-clockwise me-2"></i>Reset Period
        </button>
    </form>
    @endif
    
    <a href="{{ route('maintenance-profiles.edit', $maintenanceProfile) }}" class="btn btn-outline-secondary">
        <i class="bi bi-pencil me-2"></i>Edit
    </a>
    
    <div class="dropdown">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('projects.show', $maintenanceProfile->project) }}">
                    <i class="bi bi-folder me-2"></i>View Project
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" 
                   onclick="if(confirm('Delete this maintenance profile?')) { document.getElementById('delete-form').submit(); }">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
        </ul>
    </div>
    
    <form id="delete-form" action="{{ route('maintenance-profiles.destroy', $maintenanceProfile) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Profile Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Profile Details</h5>
                @if($maintenanceProfile->maintenance_type === 'retainer')
                <span class="badge bg-primary">Retainer</span>
                @else
                <span class="badge bg-secondary">Hourly</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Project</small>
                        <a href="{{ route('projects.show', $maintenanceProfile->project) }}" class="text-decoration-none fw-semibold">
                            {{ $maintenanceProfile->project->name }}
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Client</small>
                        <a href="{{ route('clients.show', $maintenanceProfile->project->client) }}" class="text-decoration-none">
                            {{ $maintenanceProfile->project->client->name }}
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Hourly Rate</small>
                        <div class="fw-semibold">{{ $maintenanceProfile->project->client->currency ?? 'ZAR' }} {{ number_format($maintenanceProfile->rate, 2) }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Start Date</small>
                        <div>{{ $maintenanceProfile->start_date->format('M d, Y') }}</div>
                    </div>
                    @if($maintenanceProfile->maintenance_type === 'retainer')
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Monthly Hours</small>
                        <div class="fw-semibold">{{ number_format($maintenanceProfile->monthly_hours, 1) }} hours</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Last Reset</small>
                        <div>{{ $maintenanceProfile->last_reset_date ? $maintenanceProfile->last_reset_date->format('M d, Y') : 'Not reset yet' }}</div>
                    </div>
                    @endif
                </div>
                
                @if($maintenanceProfile->sla_notes)
                <div class="mt-3">
                    <small class="text-muted d-block">SLA Notes</small>
                    <div class="bg-light rounded p-3 mt-1">
                        {!! nl2br(e($maintenanceProfile->sla_notes)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Usage Stats for Retainer -->
        @if($maintenanceProfile->maintenance_type === 'retainer')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Current Period Usage</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-muted small">Monthly Allocation</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($maintenanceProfile->monthly_hours, 1) }}</div>
                            <div class="text-muted small">hours</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-muted small">Rollover</div>
                            <div class="fs-4 fw-bold text-info">{{ number_format($maintenanceProfile->rollover_hours, 1) }}</div>
                            <div class="text-muted small">hours</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-muted small">Used</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($maintenanceProfile->used_hours, 1) }}</div>
                            <div class="text-muted small">hours</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="text-muted small">Remaining</div>
                            <div class="fs-4 fw-bold {{ $maintenanceProfile->remaining_hours < 2 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($maintenanceProfile->remaining_hours, 1) }}
                            </div>
                            <div class="text-muted small">hours</div>
                        </div>
                    </div>
                </div>

                <div class="progress" style="height: 25px;">
                    <div class="progress-bar {{ $maintenanceProfile->usage_percentage > 90 ? 'bg-danger' : ($maintenanceProfile->usage_percentage > 75 ? 'bg-warning' : 'bg-success') }}" 
                         role="progressbar" 
                         style="width: {{ min(100, $maintenanceProfile->usage_percentage) }}%">
                        {{ number_format($maintenanceProfile->usage_percentage, 1) }}%
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        Period: {{ $maintenanceProfile->getCurrentPeriodStart()->format('M d, Y') }} - {{ $maintenanceProfile->getCurrentPeriodStart()->addMonth()->subDay()->format('M d, Y') }}
                    </small>
                </div>

                @if($maintenanceProfile->usage_percentage > 90)
                <div class="alert alert-danger mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Low hours remaining!</strong> Only {{ number_format($maintenanceProfile->remaining_hours, 1) }} hours left this period.
                </div>
                @elseif($maintenanceProfile->usage_percentage > 75)
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ number_format($maintenanceProfile->remaining_hours, 1) }} hours remaining this period.
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Time Entries -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recent Time Entries</h5>
            </div>
            <div class="card-body">
                @if($timeEntries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Task</th>
                                <th>Description</th>
                                <th class="text-end">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeEntries as $entry)
                            <tr>
                                <td>{{ $entry->start_time->format('M d, Y') }}</td>
                                <td>{{ $entry->user->name }}</td>
                                <td>{{ $entry->task->name ?? 'N/A' }}</td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($entry->description, 50) }}</small>
                                </td>
                                <td class="text-end">{{ number_format($entry->duration_minutes / 60, 2) }} hrs</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total:</td>
                                <td class="text-end">{{ number_format($timeEntries->sum('duration_minutes') / 60, 2) }} hrs</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-clock-history display-4 text-muted"></i>
                    <p class="text-muted mt-2">No time entries logged yet for this period.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Type:</span>
                    <span class="fw-semibold">{{ ucfirst($maintenanceProfile->maintenance_type) }}</span>
                </div>
                @if($maintenanceProfile->maintenance_type === 'retainer')
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Monthly Value:</span>
                    <span class="fw-semibold">{{ $maintenanceProfile->project->client->currency ?? 'ZAR' }} {{ number_format($maintenanceProfile->monthly_hours * $maintenanceProfile->rate, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Value Used:</span>
                    <span class="fw-semibold">{{ $maintenanceProfile->project->client->currency ?? 'ZAR' }} {{ number_format($maintenanceProfile->used_hours * $maintenanceProfile->rate, 2) }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Time Entries:</span>
                    <span class="fw-semibold">{{ $timeEntries->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('projects.show', $maintenanceProfile->project) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-folder me-2"></i>View Project
                </a>
                <a href="{{ route('time-entries.create', ['project_id' => $maintenanceProfile->project_id]) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-plus-circle me-2"></i>Log Time
                </a>
                <a href="{{ route('maintenance-profiles.edit', $maintenanceProfile) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-pencil me-2"></i>Edit Profile
                </a>
                @if($maintenanceProfile->maintenance_type === 'retainer' && $maintenanceProfile->needsReset())
                <form action="{{ route('maintenance-profiles.reset', $maintenanceProfile) }}" method="POST">
                    @csrf
                    <button type="submit" class="list-group-item list-group-item-action text-warning" 
                            onclick="return confirm('Reset this maintenance profile? Unused hours will rollover.')">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Period
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
