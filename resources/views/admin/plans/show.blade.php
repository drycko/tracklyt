@extends('admin.layout')

@section('title', $plan->name)
@section('header', $plan->name)

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-1"></i>Edit Plan
    </a>
    <form action="{{ route('admin.plans.toggle-active', $plan) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-{{ $plan->is_active ? 'warning' : 'success' }}">
            <i class="bi bi-{{ $plan->is_active ? 'pause' : 'play' }}-circle me-1"></i>
            {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
        </button>
    </form>
    <form action="{{ route('admin.plans.toggle-featured', $plan) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-warning">
            <i class="bi bi-star{{ $plan->is_featured ? '-fill' : '' }} me-1"></i>
            {{ $plan->is_featured ? 'Unfeature' : 'Feature' }}
        </button>
    </form>
</div>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Summary Cards -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-people fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active</h6>
                            <h3 class="mb-0">{{ $activeSubscriptions }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Trial</h6>
                            <h3 class="mb-0">{{ $trialSubscriptions }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-currency-dollar fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">MRR</h6>
                            <h3 class="mb-0">{{ format_money($totalMRR) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-check-circle fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Status</h6>
                            <h5 class="mb-0">
                                @if($plan->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Details -->
    <div class="row mt-4">
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Plan Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Plan Name</label>
                            <div class="fw-semibold">{{ $plan->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Slug</label>
                            <div class="fw-semibold"><code>{{ $plan->slug }}</code></div>
                        </div>
                    </div>

                    @if($plan->description)
                    <div class="mb-3">
                        <label class="text-muted small">Description</label>
                        <div>{{ $plan->description }}</div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-muted small">Monthly Price</label>
                            <div class="fs-4 text-primary fw-bold">{{ format_money($plan->price_monthly) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Yearly Price</label>
                            <div class="fs-4 text-primary fw-bold">{{ format_money($plan->price_yearly) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Limits -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Limits</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people text-primary me-2 fs-4"></i>
                                <div>
                                    <div class="small text-muted">Max Users</div>
                                    <div class="fw-semibold">{{ $plan->max_users === -1 ? 'Unlimited' : $plan->max_users }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder text-primary me-2 fs-4"></i>
                                <div>
                                    <div class="small text-muted">Max Projects</div>
                                    <div class="fw-semibold">{{ $plan->max_projects === -1 ? 'Unlimited' : $plan->max_projects }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-badge text-primary me-2 fs-4"></i>
                                <div>
                                    <div class="small text-muted">Max Clients</div>
                                    <div class="fw-semibold">{{ $plan->max_clients === -1 ? 'Unlimited' : $plan->max_clients }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock text-primary me-2 fs-4"></i>
                                <div>
                                    <div class="small text-muted">Monthly Hours</div>
                                    <div class="fw-semibold">{{ $plan->max_monthly_hours === -1 ? 'Unlimited' : $plan->max_monthly_hours }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-receipt text-primary me-2 fs-4"></i>
                                <div>
                                    <div class="small text-muted">Invoices/Month</div>
                                    <div class="fw-semibold">{{ $plan->max_invoices_per_month === -1 ? 'Unlimited' : $plan->max_invoices_per_month }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-chat text-primary me-2 fs-4"></i>
                                <div>
                                    <div class="small text-muted">SMS/Month</div>
                                    <div class="fw-semibold">{{ $plan->max_twilio_messages_per_month === -1 ? 'Unlimited' : $plan->max_twilio_messages_per_month }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Features</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_time_tracking ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>Time Tracking</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_invoicing ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>Invoicing</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_client_portal ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>Client Portal</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_maintenance_reports ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>Maintenance Reports</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_advanced_reporting ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>Advanced Reporting</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_api_access ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>API Access</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_white_label ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>White Label</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $plan->has_priority_support ? 'check-circle-fill text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span>Priority Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Metadata -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Metadata</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <div>
                            @if($plan->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Featured</label>
                        <div>
                            @if($plan->is_featured)
                                <i class="bi bi-star-fill text-warning"></i> Yes
                            @else
                                <i class="bi bi-star text-muted"></i> No
                            @endif
                        </div>
                    </div>

                    @if($plan->display_order)
                    <div class="mb-3">
                        <label class="text-muted small">Display Order</label>
                        <div>{{ $plan->display_order }}</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="text-muted small">Created</label>
                        <div>{{ $plan->created_at->format('M d, Y') }}</div>
                    </div>

                    <div>
                        <label class="text-muted small">Last Updated</label>
                        <div>{{ $plan->updated_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            @if($plan->subscriptions_count === 0)
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger bg-opacity-10 border-bottom border-danger py-3">
                    <h6 class="mb-0 text-danger">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3">Permanently delete this plan. This action cannot be undone.</p>
                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deletePlanModal">
                        <i class="bi bi-trash me-1"></i>Delete Plan
                    </button>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deletePlanModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete <strong>{{ $plan->name }}</strong>?</p>
                            <p class="text-danger small">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete Plan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
