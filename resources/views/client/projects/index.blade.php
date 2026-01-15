@extends('layouts.client')

@section('title', 'My Projects')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold mb-1">My Projects</h2>
        <p class="text-muted">View all your active and completed projects</p>
    </div>
</div>

@if($projects->count() > 0)
<div class="row g-4">
    @foreach($projects as $project)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0">{{ $project->name }}</h5>
                    <span class="badge {{ $project->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $project->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <p class="card-text text-muted small">{{ Str::limit($project->description, 100) }}</p>
                
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        {{ $project->start_date ? $project->start_date->format('M d, Y') : 'No start date' }}
                    </small>
                </div>
                
                <!-- Quick Stats -->
                <div class="row g-2 mb-3">
                    @if($project->repositories->count() > 0)
                    <div class="col-6">
                        <small class="text-muted">
                            <i class="bi bi-code-slash me-1"></i>{{ $project->repositories->count() }} Repo(s)
                        </small>
                    </div>
                    @endif
                    @if($project->links->count() > 0)
                    <div class="col-6">
                        <small class="text-muted">
                            <i class="bi bi-link-45deg me-1"></i>{{ $project->links->count() }} Link(s)
                        </small>
                    </div>
                    @endif
                </div>
                
                <a href="{{ route('client.projects.show', $project) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-eye me-1"></i>View Details
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $projects->links() }}
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-briefcase text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3">No Projects Found</h5>
                <p class="text-muted">You don't have any projects yet.</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
