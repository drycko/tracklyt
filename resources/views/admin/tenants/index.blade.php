@extends('admin.layout')

@section('title', 'Manage Tenants')
@section('header', 'Tenant Management')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Create Tenant
    </a>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="bi bi-funnel me-1"></i>Filter
    </button>
</div>
@endsection

@section('content')
    <!-- Summary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-buildings fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Tenants</h6>
                            <h3 class="mb-0">{{ $tenants->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-check-circle fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active</h6>
                            <h3 class="mb-0">{{ $tenants->where('status', 'active')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">On Trial</h6>
                            <h3 class="mb-0">{{ $tenants->where('status', 'trial')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-pause-circle fs-2 text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Suspended</h6>
                            <h3 class="mb-0">{{ $tenants->where('status', 'suspended')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenants -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($tenants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Contact</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenants as $tenant)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-decoration-none fw-semibold">
                                            {{ $tenant->name }}
                                        </a>
                                        @if($tenant->domain)
                                            <div class="small text-muted">{{ $tenant->domain }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $tenant->contact_name ?? '-' }}
                                        @if($tenant->contact_email)
                                            <div class="small text-muted">{{ $tenant->contact_email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tenant->subscription)
                                            {{ $tenant->subscription->plan->name }}
                                        @else
                                            <span class="text-muted">No Plan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : 
                                                                 ($tenant->status === 'trial' ? 'warning' : 
                                                                 ($tenant->status === 'suspended' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $tenant->created_at->format('M j, Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.tenants.show', $tenant) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tenants.edit', $tenant) }}" 
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($tenant->status === 'active')
                                                <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning btn-sm"
                                                            onclick="return confirm('Suspend this tenant?')" title="Suspend">
                                                        <i class="bi bi-pause"></i>
                                                    </button>
                                                </form>
                                            @elseif($tenant->status === 'suspended')
                                                <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success btn-sm"
                                                            onclick="return confirm('Activate this tenant?')" title="Activate">
                                                        <i class="bi bi-play"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-buildings display-1 text-muted"></i>
                    <h4 class="mt-3">No tenants yet</h4>
                    <p class="text-muted">Start by creating your first tenant</p>
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Tenant
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    @if($tenants->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $tenants->links() }}
        </div>
    @endif
@endsection