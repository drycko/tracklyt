@extends('admin.layout')

@section('title', 'Subscription Plans')
@section('header', 'Subscription Plans')

@section('actions')
<a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-1"></i>Create Plan
</a>
@endsection

@section('content')
    <!-- Summary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-diagram-3 fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Plans</h6>
                            <h3 class="mb-0">{{ $plans->count() }}</h3>
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
                            <h6 class="text-muted mb-1">Active Plans</h6>
                            <h3 class="mb-0">{{ $plans->where('is_active', true)->count() }}</h3>
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
                            <i class="bi bi-star fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Featured Plans</h6>
                            <h3 class="mb-0">{{ $plans->where('is_featured', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-people fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Subscriptions</h6>
                            <h3 class="mb-0">{{ $plans->sum('subscriptions_count') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0">All Plans</h5>
        </div>
        <div class="card-body p-0">
            @if($plans->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-diagram-3 fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No subscription plans found.</p>
                    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create Your First Plan
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Plan Name</th>
                                <th>Pricing</th>
                                <th>Subscriptions</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-semibold">{{ $plan->name }}</div>
                                                <small class="text-muted">{{ $plan->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold text-primary">{{ format_money($plan->price_monthly) }}/mo</div>
                                            <small class="text-muted">{{ format_money($plan->price_yearly) }}/yr</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $plan->subscriptions_count }}</span>
                                    </td>
                                    <td>
                                        @if($plan->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plan->is_featured)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-muted"></i>
                                        @endif
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePlanModal{{ $plan->id }}" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deletePlanModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Delete Plan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the plan <strong>{{ $plan->name }}</strong>?</p>
                                                        @if($plan->subscriptions_count > 0)
                                                            <div class="alert alert-warning">
                                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                                This plan has <strong>{{ $plan->subscriptions_count }}</strong> active subscription(s). You cannot delete it until all subscriptions are removed.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" @if($plan->subscriptions_count > 0) disabled @endif>
                                                                Delete Plan
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
