@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('header', 'Platform Dashboard')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.revenue') }}" class="btn btn-outline-primary">
        <i class="bi bi-graph-up me-1"></i>Revenue Analytics
    </a>
    <a href="{{ route('admin.usage') }}" class="btn btn-outline-info">
        <i class="bi bi-speedometer me-1"></i>Usage Analytics
    </a>
</div>
@endsection

@section('content')
    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <!-- Total Tenants -->
        <div class="col-xl-3 col-md-6">
            <div class="card admin-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-buildings fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Tenants</h6>
                            <h3 class="mb-0">{{ number_format($totalTenants) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        <span class="badge bg-success me-2">{{ number_format($activeTenants) }}</span>active
                        <span class="badge bg-warning ms-2">{{ number_format($trialTenants) }}</span>on trial
                    </small>
                </div>
            </div>
        </div>

        <!-- Monthly Recurring Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card admin-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-currency-dollar fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Monthly Recurring Revenue</h6>
                            <h3 class="mb-0">${{ number_format($mrr, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        ARR: <span class="fw-bold text-success">${{ number_format($arr, 2) }}</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="col-xl-3 col-md-6">
            <div class="card admin-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-credit-card-2-front fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Subscriptions</h6>
                            <h3 class="mb-0">{{ number_format($activeSubscriptions) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        <span class="badge bg-danger me-1">{{ number_format($canceledSubscriptions) }}</span>canceled
                    </small>
                </div>
            </div>
        </div>

        <!-- Monthly Growth -->
        <div class="col-xl-3 col-md-6">
            <div class="card admin-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-graph-up-arrow fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Monthly Growth</h6>
                            <h3 class="mb-0 {{ $monthlyGrowth['growth_rate'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $monthlyGrowth['growth_rate'] }}%
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        {{ $monthlyGrowth['current_month'] }} this month vs {{ $monthlyGrowth['last_month'] }} last month
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="row g-4 mb-4">
        <!-- Plan Distribution Chart -->
        <div class="col-lg-6">
            <div class="card admin-card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Plan Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="planDistributionChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Twilio Usage Stats -->
        <div class="col-lg-6">
            <div class="card admin-card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Twilio Usage (This Month)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-whatsapp fs-3 text-success mb-2"></i>
                                <h4 class="mb-1">{{ number_format($twilioUsageStats['total_whatsapp']) }}</h4>
                                <small class="text-muted">WhatsApp Messages</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-chat-text fs-3 text-primary mb-2"></i>
                                <h4 class="mb-1">{{ number_format($twilioUsageStats['total_sms']) }}</h4>
                                <small class="text-muted">SMS Messages</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <h5 class="mb-0">{{ number_format($twilioUsageStats['total_messages']) }}</h5>
                                    <small class="text-muted">Total Messages</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0 text-success">${{ number_format($twilioUsageStats['total_cost'], 2) }}</h5>
                                    <small class="text-muted">Total Cost</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Recent Tenants -->
        <div class="col-lg-6">
            <div class="card admin-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Tenants
                    </h5>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentTenants as $tenant)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <span class="fw-bold text-primary">{{ substr($tenant->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-decoration-none">
                                                {{ $tenant->name }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $tenant->subscription?->plan?->name ?? 'No subscription' }} • 
                                            {{ $tenant->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'suspended' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="text-muted mb-0">No tenants yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiring Trials -->
        <div class="col-lg-6">
            <div class="card admin-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Trials Expiring Soon
                    </h5>
                    <a href="{{ route('admin.subscriptions.index', ['status' => 'trial']) }}" class="btn btn-sm btn-outline-warning">View all trials</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($expiringTrials as $subscription)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-clock text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-decoration-none">
                                                {{ $subscription->tenant->name }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $subscription->plan->name }} • 
                                            Expires {{ $subscription->trial_ends_at->format('M j, Y') }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <small class="text-warning fw-bold">
                                            {{ $subscription->trial_ends_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="text-muted mb-0">No trials expiring soon.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Plan Distribution Chart
        const ctx = document.getElementById('planDistributionChart').getContext('2d');
        const planData = @json($planDistribution);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(planData),
                datasets: [{
                    data: Object.values(planData),
                    backgroundColor: [
                        '#0d6efd', // Primary
                        '#198754', // Success
                        '#ffc107', // Warning
                        '#dc3545', // Danger
                        '#6f42c1', // Purple
                    ],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    });
</script>
@endpush