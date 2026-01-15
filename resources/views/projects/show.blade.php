@extends('layouts.app')

@section('title', $project->name)
@section('header', $project->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
<li class="breadcrumb-item active">{{ $project->name }}</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary">
        <i class="bi bi-pencil me-2"></i>Edit
    </a>

    <div class="dropdown">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Export Report</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-archive me-2"></i>Archive Project</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" 
                   onclick="if(confirm('Are you sure? This will delete all related data.')) { document.getElementById('delete-form').submit(); }">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
            <form id="delete-form" action="{{ route('projects.destroy', $project) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Project Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Project Details</h5>
                <div>
                    @if($project->is_active)
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-secondary">Inactive</span>
                    @endif
                    
                    @if($project->project_type === 'new_build')
                    <span class="badge bg-primary"><i class="bi bi-hammer"></i> New Build</span>
                    @else
                    <span class="badge bg-info"><i class="bi bi-wrench"></i> Maintenance</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Client</small>
                        <a href="{{ route('clients.show', $project->client) }}" class="fw-semibold text-decoration-none">
                            {{ $project->client->name }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Billing Type</small>
                        <span class="fw-semibold">
                            @if($project->billing_type === 'hourly')
                            <span class="badge bg-success">Hourly</span>
                            @if($project->hourly_rate)
                            <span class="ms-2">${{ number_format($project->hourly_rate, 2) }}/hr</span>
                            @endif
                            @elseif($project->billing_type === 'fixed')
                            <span class="badge bg-warning text-dark">Fixed Price</span>
                            @else
                            <span class="badge bg-secondary">Retainer</span>
                            @endif
                        </span>
                    </div>
                </div>

                @if($project->quote)
                <div class="mb-3">
                    <small class="text-muted d-block">Created From Quote</small>
                    <a href="{{ route('quotes.show', $project->quote) }}" class="text-decoration-none">
                        <i class="bi bi-file-earmark-text me-1"></i>{{ $project->quote->quote_number }}
                    </a>
                </div>
                @endif

                @if($project->description)
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <div class="bg-light rounded p-3 markdown-content">
                        {!! markdown($project->description) !!}
                    </div>
                </div>
                @endif

                <div class="row mt-4">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Estimated Hours</small>
                        <div class="fs-5 fw-semibold">
                            @if($project->estimated_hours)
                            {{ number_format($project->estimated_hours, 1) }} hrs
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Start Date</small>
                        <div class="fs-6 fw-semibold">
                            @if($project->start_date)
                            {{ $project->start_date->format('M d, Y') }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">End Date</small>
                        <div class="fs-6 fw-semibold">
                            @if($project->end_date)
                            {{ $project->end_date->format('M d, Y') }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Source</small>
                        <div class="fs-6">
                            @if($project->source === 'quote')
                            <i class="bi bi-file-earmark-text"></i> Quote
                            @else
                            <i class="bi bi-person-check"></i> Direct
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repositories -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Repositories</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addRepositoryModal">
                    <i class="bi bi-plus me-1"></i>Add Repository
                </button>
            </div>
            <div class="card-body">
                @if($project->repositories->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($project->repositories as $repo)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    @if($repo->provider === 'github')
                                    <i class="bi bi-github me-1"></i>
                                    @elseif($repo->provider === 'gitlab')
                                    <i class="bi bi-git me-1" style="color: #fc6d26;"></i>
                                    @else
                                    <i class="bi bi-git me-1" style="color: #0052cc;"></i>
                                    @endif
                                    {{ $repo->repo_name }}
                                    @if($repo->is_primary)
                                    <span class="badge bg-primary badge-sm">Primary</span>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $repo->repo_url }}</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ $repo->repo_url }}" target="_blank" class="btn btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="editRepository({{ $repo->id }}, '{{ $repo->provider }}', '{{ $repo->repo_url }}', {{ $repo->is_primary ? 'true' : 'false' }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteRepository({{ $repo->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0 text-center py-3">No repositories added yet.</p>
                @endif
            </div>
        </div>

        <!-- Links -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Project Links</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addLinkModal">
                    <i class="bi bi-plus me-1"></i>Add Link
                </button>
            </div>
            <div class="card-body">
                @if($project->links->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($project->links as $link)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    <i class="bi bi-link-45deg me-1"></i>{{ $link->label }}
                                    @if($link->type === 'production')
                                    <span class="badge bg-success badge-sm">Production</span>
                                    @elseif($link->type === 'staging')
                                    <span class="badge bg-warning text-dark badge-sm">Staging</span>
                                    @else
                                    <span class="badge bg-info badge-sm">Demo</span>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $link->url }}</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ $link->url }}" target="_blank" class="btn btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="editLink({{ $link->id }}, '{{ $link->type }}', '{{ $link->label }}', '{{ $link->url }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteLink({{ $link->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0 text-center py-3">No links added yet.</p>
                @endif
            </div>
        </div>

        <!-- Mobile Apps -->
        @if($project->project_type === 'new_build')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Mobile Apps</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMobileAppModal">
                    <i class="bi bi-plus me-1"></i>Add Mobile App
                </button>
            </div>
            <div class="card-body">
                @if($project->mobileApps->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($project->mobileApps as $app)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    @if($app->platform === 'android')
                                    <i class="bi bi-google-play me-1" style="color: #3DDC84;"></i>
                                    @else
                                    <i class="bi bi-apple me-1"></i>
                                    @endif
                                    {{ $app->app_name }}
                                    <span class="badge bg-secondary badge-sm">{{ ucfirst($app->platform) }}</span>
                                </h6>
                                <small class="text-muted d-block">{{ $app->package_name }}</small>
                                @if($app->current_version)
                                <small class="text-muted">Version: {{ $app->current_version }}</small>
                                @endif
                            </div>
                            <div class="btn-group btn-group-sm">
                                @if($app->store_url)
                                <a href="{{ $app->store_url }}" target="_blank" class="btn btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @endif
                                <button type="button" class="btn btn-outline-secondary" onclick="editMobileApp({{ $app->id }}, '{{ $app->platform }}', '{{ $app->app_name }}', '{{ $app->package_name }}', '{{ $app->app_store_url }}', '{{ $app->play_store_url }}', '{{ $app->current_version }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteMobileApp({{ $app->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0 text-center py-3">No mobile apps added yet.</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Tasks -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Tasks</h5>
                <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($project->tasks->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($project->tasks->take(5) as $task)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $task->title }}</h6>
                                <small class="text-muted">
                                    @if($task->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                    @elseif($task->status === 'in_progress')
                                    <span class="badge bg-primary">In Progress</span>
                                    @else
                                    <span class="badge bg-secondary">To Do</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0 text-center py-3">No tasks yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Project Stats</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Total Time Logged</small>
                    <h4 class="mb-0">{{ number_format($project->timeEntries->sum('hours'), 1) }} hrs</h4>
                    @if($project->estimated_hours)
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ min(($project->timeEntries->sum('hours') / $project->estimated_hours) * 100, 100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format(min(($project->timeEntries->sum('hours') / $project->estimated_hours) * 100, 100), 1) }}% of estimated</small>
                    @endif
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted d-block">Tasks</small>
                    <h4 class="mb-0">{{ $project->tasks->where('status', 'completed')->count() }}/{{ $project->tasks->count() }}</h4>
                    <small class="text-muted">Completed</small>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted d-block">Repositories</small>
                    <h4 class="mb-0">{{ $project->repositories->count() }}</h4>
                </div>

                <hr>

                <div class="mb-0">
                    <small class="text-muted d-block">Project Links</small>
                    <h4 class="mb-0">{{ $project->links->count() }}</h4>
                </div>
            </div>
        </div>

        <!-- Recent Time Entries -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Recent Time Entries</h5>
            </div>
            <div class="card-body">
                @if($project->timeEntries->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($project->timeEntries->take(5) as $entry)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">{{ $entry->date->format('M d') }}</small>
                            <strong>{{ number_format($entry->hours, 2) }} hrs</strong>
                        </div>
                        @if($entry->description)
                        <small class="text-muted d-block">{{ Str::limit($entry->description, 50) }}</small>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('time-entries.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-primary">
                        View All Time Entries
                    </a>
                </div>
                @else
                <p class="text-muted mb-0 text-center py-3">No time entries yet.</p>
                @endif
            </div>
        </div>

        <!-- Project Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="d-flex mb-3">
                        <div class="text-muted small" style="min-width: 80px;">Created</div>
                        <div class="ms-3">
                            <strong>{{ $project->created_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                    @if($project->start_date)
                    <div class="d-flex mb-3">
                        <div class="text-muted small" style="min-width: 80px;">Started</div>
                        <div class="ms-3">
                            <strong>{{ $project->start_date->format('M d, Y') }}</strong>
                        </div>
                    </div>
                    @endif
                    @if($project->end_date)
                    <div class="d-flex mb-3">
                        <div class="text-muted small" style="min-width: 80px;">Due</div>
                        <div class="ms-3">
                            <strong>{{ $project->end_date->format('M d, Y') }}</strong>
                            @if($project->end_date->isPast())
                            <span class="badge bg-danger ms-2">Overdue</span>
                            @elseif($project->end_date->diffInDays(now()) <= 7)
                            <span class="badge bg-warning ms-2">Due Soon</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Repository Modal -->
<div class="modal fade" id="addRepositoryModal" tabindex="-1" aria-labelledby="addRepositoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('projects.repositories.store', $project) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addRepositoryModalLabel">Add Repository</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="provider" class="form-label">Provider <span class="text-danger">*</span></label>
                        <select class="form-select" id="provider" name="provider" required>
                            <option value="">Select Provider</option>
                            <option value="github">GitHub</option>
                            <option value="gitlab">GitLab</option>
                            <option value="bitbucket">Bitbucket</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="repo_url" class="form-label">Repository URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="repo_url" name="repo_url" placeholder="https://github.com/username/repo" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary">
                            Set as Primary Repository
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Repository</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Repository Modal -->
<div class="modal fade" id="editRepositoryModal" tabindex="-1" aria-labelledby="editRepositoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editRepositoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editRepositoryModalLabel">Edit Repository</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_provider" class="form-label">Provider <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_provider" name="provider" required>
                            <option value="">Select Provider</option>
                            <option value="github">GitHub</option>
                            <option value="gitlab">GitLab</option>
                            <option value="bitbucket">Bitbucket</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_repo_url" class="form-label">Repository URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="edit_repo_url" name="repo_url" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="edit_is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="edit_is_primary">
                            Set as Primary Repository
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Repository</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Link Modal -->
<div class="modal fade" id="addLinkModal" tabindex="-1" aria-labelledby="addLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('projects.links.store', $project) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addLinkModalLabel">Add Project Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="production">Production</option>
                            <option value="staging">Staging</option>
                            <option value="demo">Demo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="label" name="label" placeholder="e.g., Live Website, Admin Panel" required>
                    </div>
                    <div class="mb-3">
                        <label for="url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Link Modal -->
<div class="modal fade" id="editLinkModal" tabindex="-1" aria-labelledby="editLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editLinkForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editLinkModalLabel">Edit Project Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="production">Production</option>
                            <option value="staging">Staging</option>
                            <option value="demo">Demo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_label" class="form-label">Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_label" name="label" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="edit_url" name="url" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Forms -->
<form id="deleteRepositoryForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<form id="deleteLinkForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<form id="deleteMobileAppForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Add Mobile App Modal -->
<div class="modal fade" id="addMobileAppModal" tabindex="-1" aria-labelledby="addMobileAppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('projects.mobile-apps.store', $project) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addMobileAppModalLabel">Add Mobile App</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="platform" class="form-label">Platform <span class="text-danger">*</span></label>
                        <select class="form-select" id="platform" name="platform" required>
                            <option value="">Select Platform</option>
                            <option value="ios">iOS</option>
                            <option value="android">Android</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="app_name" class="form-label">App Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="app_name" name="app_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="package_name" class="form-label">Package/Bundle ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="package_name" name="package_name" placeholder="com.example.app" required>
                        <small class="text-muted">e.g., com.example.app or com.company.appname</small>
                    </div>
                    <div class="mb-3">
                        <label for="app_store_url" class="form-label">App Store URL</label>
                        <input type="url" class="form-control" id="app_store_url" name="app_store_url" placeholder="https://apps.apple.com/...">
                    </div>
                    <div class="mb-3">
                        <label for="play_store_url" class="form-label">Play Store URL</label>
                        <input type="url" class="form-control" id="play_store_url" name="play_store_url" placeholder="https://play.google.com/...">
                    </div>
                    <div class="mb-3">
                        <label for="current_version" class="form-label">Current Version</label>
                        <input type="text" class="form-control" id="current_version" name="current_version" placeholder="1.0.0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Mobile App</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Mobile App Modal -->
<div class="modal fade" id="editMobileAppModal" tabindex="-1" aria-labelledby="editMobileAppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editMobileAppForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editMobileAppModalLabel">Edit Mobile App</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_platform" class="form-label">Platform <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_platform" name="platform" required>
                            <option value="">Select Platform</option>
                            <option value="ios">iOS</option>
                            <option value="android">Android</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_app_name" class="form-label">App Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_app_name" name="app_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_package_name" class="form-label">Package/Bundle ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_package_name" name="package_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_app_store_url" class="form-label">App Store URL</label>
                        <input type="url" class="form-control" id="edit_app_store_url" name="app_store_url">
                    </div>
                    <div class="mb-3">
                        <label for="edit_play_store_url" class="form-label">Play Store URL</label>
                        <input type="url" class="form-control" id="edit_play_store_url" name="play_store_url">
                    </div>
                    <div class="mb-3">
                        <label for="edit_current_version" class="form-label">Current Version</label>
                        <input type="text" class="form-control" id="edit_current_version" name="current_version">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Mobile App</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editRepository(id, provider, repoUrl, isPrimary) {
        const form = document.getElementById('editRepositoryForm');
        form.action = '/repositories/' + id;
        
        document.getElementById('edit_provider').value = provider;
        document.getElementById('edit_repo_url').value = repoUrl;
        document.getElementById('edit_is_primary').checked = isPrimary;
        
        new bootstrap.Modal(document.getElementById('editRepositoryModal')).show();
    }

    function deleteRepository(id) {
        if (confirm('Are you sure you want to delete this repository?')) {
            const form = document.getElementById('deleteRepositoryForm');
            form.action = '/repositories/' + id;
            form.submit();
        }
    }

    function editLink(id, type, label, url) {
        const form = document.getElementById('editLinkForm');
        form.action = '/links/' + id;
        
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_label').value = label;
        document.getElementById('edit_url').value = url;
        
        new bootstrap.Modal(document.getElementById('editLinkModal')).show();
    }

    function deleteLink(id) {
        if (confirm('Are you sure you want to delete this link?')) {
            const form = document.getElementById('deleteLinkForm');
            form.action = '/links/' + id;
            form.submit();
        }
    }

    function editMobileApp(id, platform, appName, packageName, appStoreUrl, playStoreUrl, currentVersion) {
        const form = document.getElementById('editMobileAppForm');
        form.action = '/mobile-apps/' + id;
        
        document.getElementById('edit_platform').value = platform;
        document.getElementById('edit_app_name').value = appName;
        document.getElementById('edit_package_name').value = packageName;
        document.getElementById('edit_app_store_url').value = appStoreUrl || '';
        document.getElementById('edit_play_store_url').value = playStoreUrl || '';
        document.getElementById('edit_current_version').value = currentVersion || '';
        
        new bootstrap.Modal(document.getElementById('editMobileAppModal')).show();
    }

    function deleteMobileApp(id) {
        if (confirm('Are you sure you want to delete this mobile app?')) {
            const form = document.getElementById('deleteMobileAppForm');
            form.action = '/mobile-apps/' + id;
            form.submit();
        }
    }
</script>
@endpush
