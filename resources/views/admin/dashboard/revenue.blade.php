@extends('admin.layout')

@section('title', 'Revenue Analytics')
@section('header', 'Revenue Analytics')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
        <i class="bi bi-download me-1"></i>Export Data
    </button>
</div>
@endsection

@section('content')
    <!-- Key Revenue Metrics -->
    <div class="row g-4 mb-4">
        <!-- Monthly Recurring Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-currency-dollar fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Monthly Recurring Revenue</h6>
                            <h3 class="mb-0">${{ number_format($totalMrr ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        Based on active subscriptions
                    </small>
                </div>
            </div>
        </div>

        <!-- Total Plans -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-box-seam fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Plans</h6>
                            <h3 class="mb-0">{{ count($planRevenue ?? []) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        Revenue generating plans
                    </small>
                </div>
            </div>
        </div>

        <!-- Average Revenue Per User -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-person-dollar fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Average Revenue Per User</h6>
                            <h3 class="mb-0">${{ number_format($averageArpu ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        Per active subscription
                    </small>
                </div>
            </div>
        </div>

        <!-- Projected Annual Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-graph-up-arrow fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Annual Recurring Revenue</h6>
                            <h3 class="mb-0">${{ number_format(($totalMrr ?? 0) * 12, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <small class="text-muted">
                        Projected from current MRR
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Monthly Revenue Trend -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Monthly Revenue Trend
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Plan Revenue Breakdown -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Revenue by Plan
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="planRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Performance Summary -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-trophy me-2"></i>Plan Performance Summary
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Plan Name</th>
                            <th class="text-end">Active Subscriptions</th>
                            <th class="text-end">Monthly Revenue</th>
                            <th class="text-end">Annual Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($planRevenue ?? []) as $planId => $data)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-2 p-2">
                                            <i class="bi bi-box text-primary"></i>
                                        </div>
                                        <div class="ms-3">
                                            <div class="fw-medium">{{ $data['plan_name'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-medium">{{ number_format($data['count'] ?? 0) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-medium text-success">${{ number_format($data['mrr'] ?? 0, 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-muted">${{ number_format(($data['mrr'] ?? 0) * 12, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="bi bi-graph-up fs-1 text-muted"></i>
                                    </div>
                                    <h5 class="text-muted">No revenue data available</h5>
                                    <p class="text-muted mb-0">Revenue data will appear here once subscriptions are active.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthlyRevenueData = @json($monthlyRevenue ?? []);
        const planRevenueData = @json($planRevenue ?? []);

        // Monthly Revenue Trend Chart
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: Object.keys(monthlyRevenueData),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: Object.values(monthlyRevenueData),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Plan Revenue Chart
        const planCtx = document.getElementById('planRevenueChart').getContext('2d');
        const planLabels = Object.values(planRevenueData).map(item => item.plan_name || 'Unknown');
        const planValues = Object.values(planRevenueData).map(item => item.mrr || 0);
        
        new Chart(planCtx, {
            type: 'doughnut',
            data: {
                labels: planLabels,
                datasets: [{
                    data: planValues,
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