@extends('layouts.app')

@section('title', 'Edit Project')
@section('header', 'Edit Project')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <!-- Client -->
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quote (optional) -->
                    <div class="mb-3">
                        <label for="quote_id" class="form-label">From Quote (Optional)</label>
                        <select class="form-select @error('quote_id') is-invalid @enderror" id="quote_id" name="quote_id">
                            <option value="">No Quote</option>
                            @foreach($quotes as $quote)
                            <option value="{{ $quote->id }}" {{ old('quote_id', $project->quote_id) == $quote->id ? 'selected' : '' }}>
                                {{ $quote->quote_number }} - {{ $quote->client->name }} - {{ $quote->title }}
                            </option>
                            @endforeach
                        </select>
                        @error('quote_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Project Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $project->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Project Type -->
                        <div class="col-md-6 mb-3">
                            <label for="project_type" class="form-label">Project Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_type') is-invalid @enderror" id="project_type" name="project_type" required>
                                <option value="">Select Type</option>
                                <option value="new_build" {{ old('project_type', $project->project_type) == 'new_build' ? 'selected' : '' }}>New Build</option>
                                <option value="maintenance_takeover" {{ old('project_type', $project->project_type) == 'maintenance_takeover' ? 'selected' : '' }}>Maintenance/Takeover</option>
                            </select>
                            @error('project_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Source -->
                        <div class="col-md-6 mb-3">
                            <label for="source" class="form-label">Source <span class="text-danger">*</span></label>
                            <select class="form-select @error('source') is-invalid @enderror" id="source" name="source" required>
                                <option value="">Select Source</option>
                                <option value="quote" {{ old('source', $project->source) == 'quote' ? 'selected' : '' }}>From Quote</option>
                                <option value="direct" {{ old('source', $project->source) == 'direct' ? 'selected' : '' }}>Direct</option>
                            </select>
                            @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Billing & Budget</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Billing Type -->
                        <div class="col-md-4 mb-3">
                            <label for="billing_type" class="form-label">Billing Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('billing_type') is-invalid @enderror" id="billing_type" name="billing_type" required>
                                <option value="">Select Type</option>
                                <option value="hourly" {{ old('billing_type', $project->billing_type) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                <option value="fixed" {{ old('billing_type', $project->billing_type) == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                                <option value="retainer" {{ old('billing_type', $project->billing_type) == 'retainer' ? 'selected' : '' }}>Retainer</option>
                            </select>
                            @error('billing_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hourly Rate -->
                        <div class="col-md-4 mb-3">
                            <label for="hourly_rate" class="form-label">Hourly Rate</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('hourly_rate') is-invalid @enderror" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $project->hourly_rate) }}" step="0.01" min="0">
                            </div>
                            @error('hourly_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estimated Hours -->
                        <div class="col-md-4 mb-3">
                            <label for="estimated_hours" class="form-label">Estimated Hours</label>
                            <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror" id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', $project->estimated_hours) }}" step="0.5" min="0">
                            @error('estimated_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Start Date -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}">
                            @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}">
                            @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Is Active -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Set as Active Project
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
