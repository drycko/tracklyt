@extends('admin.layout')

@section('title', 'Tenant Details: ' . $tenant->name)

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">{{ $tenant->name }}</h1>
            <p class="text-muted small mb-0">{{ $tenant->domain ? $tenant->domain : 'No domain set' }} • Created {{ $tenant->created_at->format('M j, Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($tenant->status === 'active')
                <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to suspend this tenant?')">
                        <i class="fas fa-pause me-1"></i>Suspend
                    </button>
                </form>
            @elseif($tenant->status === 'suspended')
                <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-play me-1"></i>Activate
                    </button>
                </form>
            @endif
            
            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Status Cards -->
    <div class="row g-3 mb-4">
        <!-- Tenant Status -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $tenant->status === 'active' ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}" 
                             style="width: 48px; height: 48px;">
                            @if($tenant->status === 'active')
                                <i class="fas fa-check-circle text-success fa-lg"></i>
                            @else
                                <i class="fas fa-times-circle text-danger fa-lg"></i>
                            @endif
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Tenant Status</h6>
                        <h5 class="mb-0 fw-semibold">{{ ucfirst($tenant->status) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Info -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px;">
                            <i class="fas fa-crown text-primary fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Subscription</h6>
                        @if($tenant->subscription)
                            <h5 class="mb-0 fw-semibold">{{ $tenant->subscription->plan->name }}</h5>
                            <small class="text-muted">{{ ucfirst($tenant->subscription->status) }}</small>
                        @else
                            <h5 class="mb-0 fw-semibold">No subscription</h5>
                        @endif
                    </div>
                </div>
                @if($tenant->subscription)
                    <div class="card-footer bg-light py-2">
                        <a href="{{ route('admin.subscriptions.show', $tenant->subscription) }}" class="text-primary text-decoration-none small">
                            View details <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Contact Info -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px;">
                            <i class="fas fa-user text-info fa-lg"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-1">Contact</h6>
                        <h6 class="mb-0 fw-semibold">{{ $tenant->contact_name }}</h6>
                        <small class="text-muted">{{ $tenant->contact_email }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tenant Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Company Name</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Domain</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->domain ?: 'Not set' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Contact Name</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->contact_name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Contact Email</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->contact_email }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'trial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Created</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($tenant->subscription)
            <!-- Subscription Details -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Subscription Details</h5>
                    <a href="{{ route('admin.subscriptions.show', $tenant->subscription) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i>Manage
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Plan</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->subscription->plan->name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $tenant->subscription->status === 'active' ? 'success' : ($tenant->subscription->status === 'trial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($tenant->subscription->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Current Period Start</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->subscription->current_period_start ? $tenant->subscription->current_period_start->format('M j, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Current Period End</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->subscription->current_period_end ? $tenant->subscription->current_period_end->format('M j, Y') : 'N/A' }}</p>
                        </div>
                        @if($tenant->subscription->trial_ends_at)
                        <div class="col-sm-6">
                            <label class="text-muted small fw-medium">Trial Ends</label>
                            <p class="mb-0 fw-semibold">{{ $tenant->subscription->trial_ends_at->format('M j, Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($tenant->status === 'active')
                            <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-sm w-100"
                                        onclick="return confirm('Are you sure you want to suspend this tenant?')">
                                    <i class="fas fa-pause me-2"></i>Suspend Tenant
                                </button>
                            </form>
                        @elseif($tenant->status === 'suspended')
                            <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                    <i class="fas fa-play me-2"></i>Activate Tenant
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Tenant
                        </a>
                        
                        @if($tenant->subscription)
                            <a href="{{ route('admin.subscriptions.show', $tenant->subscription) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-crown me-2"></i>Manage Subscription
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                    onclick="return confirm('Are you sure you want to delete this tenant? This action cannot be undone.')">
                                <i class="fas fa-trash me-2"></i>Delete Tenant
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="text-muted small">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 24px; height: 24px;">
                                    <i class="fas fa-plus text-white" style="font-size: 10px;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-1">Tenant created</p>
                                <small class="text-muted">{{ $tenant->created_at->format('M j, Y g:i A') }}</small>
                            </div>
                        </div>
                        
                        @if($tenant->subscription)
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 24px; height: 24px;">
                                    <i class="fas fa-crown text-white" style="font-size: 10px;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-1">Subscription activated</p>
                                <small class="text-muted">{{ $tenant->subscription->created_at->format('M j, Y g:i A') }}</small>
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 24px; height: 24px;">
                                    <i class="fas fa-edit text-white" style="font-size: 10px;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-1">Last updated</p>
                                <small class="text-muted">{{ $tenant->updated_at->format('M j, Y g:i A') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection