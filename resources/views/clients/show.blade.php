@extends('layouts.app')

@section('title', $client->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
<li class="breadcrumb-item active">{{ $client->name }}</li>
@endsection

@section('header', $client->name)

@section('actions')
<a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">
    <i class="bi bi-pencil me-2"></i>Edit
</a>
<a href="{{ route('quotes.create') }}?client_id={{ $client->id }}" class="btn btn-success">
    <i class="bi bi-file-text me-2"></i>New Quote
</a>
<a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-info">
    <i class="bi bi-kanban me-2"></i>New Project
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Client Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Client Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Email</label>
                        <div>{{ $client->email }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Phone</label>
                        <div>{{ $client->phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Company</label>
                        <div>{{ $client->company_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Status</label>
                        <div>
                            <span class="badge bg-{{ $client->is_active ? 'success' : 'secondary' }}">
                                {{ $client->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($client->address || $client->city || $client->state)
                <hr>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Address</label>
                        <div>
                            @if($client->address)
                                {{ $client->address }}<br>
                            @endif
                            @if($client->city || $client->state || $client->zip_code)
                                {{ $client->city }}{{ $client->state ? ', ' . $client->state : '' }} {{ $client->zip_code }}
                            @endif
                            @if($client->country)
                                <br>{{ $client->country }}
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($client->notes)
                <hr>
                <div class="row">
                    <div class="col-12">
                        <label class="text-muted small">Notes</label>
                        <div>{{ $client->notes }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Projects -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-kanban me-2"></i>Projects ({{ $client->projects->count() }})</h5>
                <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add Project
                </a>
            </div>
            <div class="card-body">
                @if($client->projects->isEmpty())
                    <p class="text-muted mb-0">No projects yet.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($client->projects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $project->name }}</h6>
                                    <small class="text-muted">{{ $project->description }}</small>
                                </div>
                                <span class="badge bg-{{ $project->is_active ? 'success' : 'secondary' }}">
                                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Quotes -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Quotes ({{ $client->quotes->count() }})</h5>
                <a href="{{ route('quotes.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle me-1"></i>Add Quote
                </a>
            </div>
            <div class="card-body">
                @if($client->quotes->isEmpty())
                    <p class="text-muted mb-0">No quotes yet.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($client->quotes as $quote)
                        <a href="{{ route('quotes.show', $quote) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $quote->quote_number }} - {{ $quote->title }}</h6>
                                    <small class="text-muted">{{ $quote->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <div><strong>${{ number_format($quote->total_amount, 2) }}</strong></div>
                                    <span class="badge bg-{{ $quote->status === 'accepted' ? 'success' : ($quote->status === 'draft' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($quote->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-bar-chart me-2"></i>Quick Stats</h6>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h4 class="mb-0">{{ $client->projects->count() }}</h4>
                        <small class="text-muted">Projects</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="mb-0">{{ $client->quotes->count() }}</h4>
                        <small class="text-muted">Quotes</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0">${{ number_format($client->quotes->where('status', 'accepted')->sum('total_amount'), 0) }}</h4>
                        <small class="text-muted">Accepted</small>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0">${{ number_format($client->quotes->where('status', 'sent')->sum('total_amount'), 0) }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-clock-history me-2"></i>Activity</h6>
                <p class="small text-muted mb-2">
                    <i class="bi bi-calendar-plus me-2"></i>Created: {{ $client->created_at->format('M d, Y') }}
                </p>
                <p class="small text-muted mb-0">
                    <i class="bi bi-calendar-check me-2"></i>Updated: {{ $client->updated_at->format('M d, Y') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
