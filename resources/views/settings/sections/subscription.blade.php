<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-box text-primary me-2"></i>Current Plan</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h3 class="mb-1">{{ $tenant->plan_name }}</h3>
                <p class="text-muted mb-3">
                    @if($tenant->isOnTrial())
                        <span class="badge bg-info">Trial - {{ $tenant->trialDaysRemaining() }} days remaining</span>
                    @elseif($tenant->subscription_status === 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($tenant->subscription_status === 'past_due')
                        <span class="badge bg-warning">Past Due</span>
                    @elseif($tenant->subscription_status === 'canceled')
                        <span class="badge bg-danger">Canceled</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($tenant->subscription_status) }}</span>
                    @endif
                </p>

                @if($tenant->monthly_amount > 0)
                <div class="mb-3">
                    <h4 class="mb-0">{{ $tenant->currency }} {{ number_format($tenant->monthly_amount, 2) }}</h4>
                    <small class="text-muted">per month</small>
                </div>
                @endif

                @if($tenant->isOnTrial() && $tenant->trial_ends_at)
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Your trial ends on <strong>{{ $tenant->trial_ends_at->format('F j, Y') }}</strong>
                </div>
                @endif
            </div>

            <div class="col-md-6">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">
                        <i class="bi bi-arrow-up-circle me-1"></i>Upgrade Plan
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-receipt me-1"></i>View Billing History
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Plan Limits & Usage -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-bar-chart text-primary me-2"></i>Plan Usage</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Users -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">
                        <i class="bi bi-people text-primary me-2"></i>Team Members
                    </span>
                    <span class="text-muted">{{ $usageStats['users']['current'] }} / {{ $usageStats['users']['limit'] }}</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar {{ $usageStats['users']['percentage'] >= 90 ? 'bg-danger' : ($usageStats['users']['percentage'] >= 70 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($usageStats['users']['percentage'], 100) }}%"></div>
                </div>
            </div>

            <!-- Projects -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">
                        <i class="bi bi-folder text-primary me-2"></i>Projects
                    </span>
                    <span class="text-muted">{{ $usageStats['projects']['current'] }} / {{ $usageStats['projects']['limit'] }}</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar {{ $usageStats['projects']['percentage'] >= 90 ? 'bg-danger' : ($usageStats['projects']['percentage'] >= 70 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($usageStats['projects']['percentage'], 100) }}%"></div>
                </div>
            </div>

            <!-- Clients -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">
                        <i class="bi bi-building text-primary me-2"></i>Clients
                    </span>
                    <span class="text-muted">{{ $usageStats['clients']['current'] }} / {{ $usageStats['clients']['limit'] }}</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar {{ $usageStats['clients']['percentage'] >= 90 ? 'bg-danger' : ($usageStats['clients']['percentage'] >= 70 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($usageStats['clients']['percentage'], 100) }}%"></div>
                </div>
            </div>

            <!-- Storage -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">
                        <i class="bi bi-hdd text-primary me-2"></i>Storage
                    </span>
                    <span class="text-muted">{{ number_format($usageStats['storage']['current'], 0) }} MB / {{ number_format($usageStats['storage']['limit'], 0) }} MB</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar {{ $usageStats['storage']['percentage'] >= 90 ? 'bg-danger' : ($usageStats['storage']['percentage'] >= 70 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($usageStats['storage']['percentage'], 100) }}%"></div>
                </div>
            </div>
        </div>

        @if($usageStats['users']['percentage'] >= 90 || $usageStats['projects']['percentage'] >= 90 || $usageStats['clients']['percentage'] >= 90 || $usageStats['storage']['percentage'] >= 90)
        <div class="alert alert-warning mt-4 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Approaching Limit!</strong> You're nearing your plan limits. Consider upgrading to continue adding resources.
        </div>
        @endif
    </div>
</div>

<!-- Available Plans -->
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-grid text-primary me-2"></i>Available Plans</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Freelancer Plan -->
            <div class="col-md-3">
                <div class="card h-100 {{ $tenant->plan === 'freelancer' ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <h6 class="fw-bold">Freelancer</h6>
                        <h4 class="mb-3">{{ $tenant->currency }} 99<small class="text-muted">/mo</small></h4>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>1 User</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>5 Projects</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>10 Clients</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>1 GB Storage</li>
                        </ul>
                        @if($tenant->plan === 'freelancer')
                        <span class="badge bg-primary w-100">Current Plan</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Agency Plan -->
            <div class="col-md-3">
                <div class="card h-100 {{ $tenant->plan === 'agency' ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <h6 class="fw-bold">Agency</h6>
                        <h4 class="mb-3">{{ $tenant->currency }} 299<small class="text-muted">/mo</small></h4>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>10 Users</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>50 Projects</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>100 Clients</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>10 GB Storage</li>
                        </ul>
                        @if($tenant->plan === 'agency')
                        <span class="badge bg-primary w-100">Current Plan</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="col-md-3">
                <div class="card h-100 {{ $tenant->plan === 'enterprise' ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <h6 class="fw-bold">Enterprise</h6>
                        <h4 class="mb-3">{{ $tenant->currency }} 999<small class="text-muted">/mo</small></h4>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Unlimited Users</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Unlimited Projects</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Unlimited Clients</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>100 GB Storage</li>
                        </ul>
                        @if($tenant->plan === 'enterprise')
                        <span class="badge bg-primary w-100">Current Plan</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Custom Plan -->
            <div class="col-md-3">
                <div class="card h-100 border-secondary">
                    <div class="card-body text-center">
                        <h6 class="fw-bold">Custom</h6>
                        <p class="text-muted small mb-3">Need something different?</p>
                        <button class="btn btn-outline-secondary btn-sm">Contact Sales</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
