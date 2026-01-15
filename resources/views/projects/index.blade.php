@extends('layouts.app')

@section('title', 'Projects')
@section('header', 'Projects')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Projects</li>
@endsection

@section('actions')
<a href="{{ route('projects.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>New Project
</a>
@endsection

@section('content')
<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('projects.index') }}">
            All Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}" href="{{ route('projects.index', ['status' => 'active']) }}">
            <span class="badge bg-success">{{ \App\Models\Project::where('is_active', true)->count() }}</span> Active
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'inactive' ? 'active' : '' }}" href="{{ route('projects.index', ['status' => 'inactive']) }}">
            <span class="badge bg-secondary">{{ \App\Models\Project::where('is_active', false)->count() }}</span> Inactive
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('type') == 'new_build' ? 'active' : '' }}" href="{{ route('projects.index', ['type' => 'new_build']) }}">
            <i class="bi bi-hammer me-1"></i> New Build
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('type') == 'maintenance_takeover' ? 'active' : '' }}" href="{{ route('projects.index', ['type' => 'maintenance_takeover']) }}">
            <i class="bi bi-wrench me-1"></i> Maintenance
        </a>
    </li>
</ul>

@if($projects->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Project Name</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Billing</th>
                        <th>Estimated Hours</th>
                        <th>Start Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>
                            <a href="{{ route('projects.show', $project) }}" class="text-decoration-none fw-semibold">
                                {{ $project->name }}
                            </a>
                            @if($project->quote)
                            <small class="text-muted d-block">
                                <i class="bi bi-file-earmark-text"></i> From {{ $project->quote->quote_number }}
                            </small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('clients.show', $project->client) }}" class="text-decoration-none">
                                {{ $project->client->name }}
                            </a>
                        </td>
                        <td>
                            @if($project->project_type === 'new_build')
                            <span class="badge bg-primary"><i class="bi bi-hammer"></i> New Build</span>
                            @else
                            <span class="badge bg-info"><i class="bi bi-wrench"></i> Maintenance</span>
                            @endif
                        </td>
                        <td>
                            @if($project->billing_type === 'hourly')
                            <span class="badge bg-success">Hourly</span>
                            @elseif($project->billing_type === 'fixed')
                            <span class="badge bg-warning text-dark">Fixed</span>
                            @else
                            <span class="badge bg-secondary">Retainer</span>
                            @endif
                        </td>
                        <td>
                            @if($project->estimated_hours)
                            {{ number_format($project->estimated_hours, 1) }} hrs
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($project->start_date)
                            {{ $project->start_date->format('M d, Y') }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($project->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary">
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
        {{ $projects->links() }}
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-folder2-open display-1 text-muted"></i>
        <h4 class="mt-3">No Projects Found</h4>
        <p class="text-muted">Get started by creating your first project.</p>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Project
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
                        <small class="text-muted d-block">Active Projects</small>
                        <h3 class="mb-0">{{ \App\Models\Project::where('is_active', true)->count() }}</h3>
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
                        <small class="text-muted d-block">New Builds</small>
                        <h3 class="mb-0">{{ \App\Models\Project::where('project_type', 'new_build')->count() }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-hammer display-6"></i>
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
                        <small class="text-muted d-block">Maintenance</small>
                        <h3 class="mb-0">{{ \App\Models\Project::where('project_type', 'maintenance_takeover')->count() }}</h3>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-wrench display-6"></i>
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
                        <small class="text-muted d-block">Total Hours</small>
                        <h3 class="mb-0">{{ number_format(\App\Models\Project::sum('estimated_hours'), 0) }}</h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-clock-history display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
