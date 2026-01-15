@extends('layouts.client')

@section('title', $project->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('client.projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">{{ $project->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1">{{ $project->name }}</h2>
        <span class="badge {{ $project->is_active ? 'bg-success' : 'bg-secondary' }} me-2">
            {{ $project->is_active ? 'Active' : 'Inactive' }}
        </span>
        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $project->project_type)) }}</span>
    </div>
</div>

<!-- Project Details -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Project Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Type:</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $project->project_type)) }}</td>
                    </tr>
                    <tr>
                        <th>Billing Type:</th>
                        <td>{{ ucfirst($project->billing_type) }}</td>
                    </tr>
                    <tr>
                        <th>Start Date:</th>
                        <td>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>End Date:</th>
                        <td>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Ongoing' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Description</h5>
                <p>{{ $project->description ?? 'No description provided.' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Repositories -->
@if($project->repositories->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-code-slash me-2"></i>Code Repositories</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($project->repositories as $repo)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-github me-2"></i>
                            <a href="{{ $repo->repo_url }}" target="_blank" class="text-decoration-none">
                                {{ $repo->repo_url }}
                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                            </a>
                            @if($repo->is_primary)
                            <span class="badge bg-primary ms-2">Primary</span>
                            @endif
                        </div>
                        <span class="badge bg-secondary">{{ ucfirst($repo->provider) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Project Links -->
@if($project->links->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Project Links</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($project->links as $link)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ $link->url }}" target="_blank" class="text-decoration-none">
                                <strong>{{ $link->label }}</strong>
                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                            </a>
                            <br>
                            <small class="text-muted">{{ $link->url }}</small>
                        </div>
                        <span class="badge bg-secondary">{{ ucfirst($link->type) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Mobile Apps -->
@if($project->mobileApps->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-phone me-2"></i>Mobile Applications</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($project->mobileApps as $app)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-{{ $app->platform == 'android' ? 'google-play' : 'apple' }} me-2"></i>
                                    {{ $app->app_name }}
                                </h6>
                                <p class="mb-2"><small class="text-muted">{{ $app->package_name }}</small></p>
                                <p class="mb-3">Version: <strong>{{ $app->current_version }}</strong></p>
                                @if($app->app_store_url)
                                <a href="{{ $app->app_store_url }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="bi bi-apple me-1"></i>App Store
                                </a>
                                @endif
                                @if($app->play_store_url)
                                <a href="{{ $app->play_store_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-google-play me-1"></i>Play Store
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
