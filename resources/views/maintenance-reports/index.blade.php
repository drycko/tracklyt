@extends('layouts.app')

@section('title', 'Maintenance Reports')
@section('header', 'Maintenance Reports')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Maintenance Reports</li>
@endsection

@section('actions')
<a href="{{ route('maintenance-reports.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>New Report
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('maintenance-reports.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="project_id" class="form-label">Project</label>
                <select class="form-select" id="project_id" name="project_id">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="type_id" class="form-label">Type</label>
                <select class="form-select" id="type_id" name="type_id">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('maintenance-reports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Reports</h6>
                        <h3 class="mb-0">{{ \App\Models\MaintenanceReport::count() }}</h3>
                    </div>
                    <div class="text-primary" style="font-size: 2rem;">
                        <i class="bi bi-file-earmark-text"></i>
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
                        <h6 class="text-muted mb-1">Draft</h6>
                        <h3 class="mb-0">{{ \App\Models\MaintenanceReport::where('status', 'draft')->count() }}</h3>
                    </div>
                    <div class="text-secondary" style="font-size: 2rem;">
                        <i class="bi bi-hourglass-split"></i>
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
                        <h6 class="text-muted mb-1">In Progress</h6>
                        <h3 class="mb-0">{{ \App\Models\MaintenanceReport::where('status', 'in_progress')->count() }}</h3>
                    </div>
                    <div class="text-warning" style="font-size: 2rem;">
                        <i class="bi bi-arrow-repeat"></i>
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
                        <h6 class="text-muted mb-1">Completed</h6>
                        <h3 class="mb-0">{{ \App\Models\MaintenanceReport::where('status', 'completed')->count() }}</h3>
                    </div>
                    <div class="text-success" style="font-size: 2rem;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reports Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($reports->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Report #</th>
                        <th>Project</th>
                        <th>Type</th>
                        <th>Assigned To</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Scheduled</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    <tr>
                        <td>
                            <a href="{{ route('maintenance-reports.show', $report) }}" class="text-decoration-none fw-semibold">
                                {{ $report->report_number }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $report->project) }}" class="text-decoration-none">
                                {{ $report->project->name }}
                            </a>
                        </td>
                        <td>{{ $report->reportType->name }}</td>
                        <td>{{ $report->assignedTo->name ?? 'Unassigned' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $report->completion_percentage }}%" 
                                         aria-valuenow="{{ $report->completion_percentage }}" 
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ $report->completion_percentage }}%</small>
                            </div>
                        </td>
                        <td>
                            @if($report->status === 'draft')
                            <span class="badge bg-secondary">Draft</span>
                            @elseif($report->status === 'in_progress')
                            <span class="badge bg-warning">In Progress</span>
                            @elseif($report->status === 'completed')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
                            @endif
                        </td>
                        <td>{{ $report->scheduled_date?->format('M d, Y') ?? 'Not scheduled' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('maintenance-reports.show', $report) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($report->status, ['draft', 'in_progress']))
                                <a href="{{ route('maintenance-reports.edit', $report) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $reports->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No maintenance reports found</h5>
            <p class="text-muted">Get started by creating your first maintenance report</p>
            <a href="{{ route('maintenance-reports.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle me-2"></i>Create Report
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
