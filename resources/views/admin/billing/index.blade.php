@extends('admin.layout')

@section('title', 'Billing & Revenue')
@section('header', 'Billing & Revenue Analytics')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.billing.report') }}" class="btn btn-outline-primary">
        <i class="bi bi-file-text me-1"></i>Detailed Report
    </a>
    <a href="{{ route('admin.billing.export') }}" class="btn btn-outline-success">
        <i class="bi bi-download me-1"></i>Export Data
    </a>
</div>
@endsection

@section('content')
    <!-- Date Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.billing.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="month" class="form-label">Month</label>
                            <select class="form-select" id="month" name="month">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="year" class="form-label">Year</label>
                            <select class="form-select" id="year" name="year">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter me-1"></i>Apply Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
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
                            <h3 class="mb-0">{{ format_money($mrr) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Annual Recurring Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-graph-up fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Annual Recurring Revenue</h6>
                            <h3 class="mb-0">{{ format_money($arr) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Churn Rate -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Churn Rate</h6>
                            <h3 class="mb-0">{{ $churnStats['churn_rate'] }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Subscriptions -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-plus-circle fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">New This Month</h6>
                            <h3 class="mb-0">{{ number_format($churnStats['new_count']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown & Trend -->
    <div class="row g-4 mb-4">
        <!-- Revenue by Plan -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Revenue by Plan</h5>
                </div>
                <div class="card-body">
                    @if(empty($revenueByPlan))
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No revenue data available</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Plan</th>
                                        <th class="text-center">Subscriptions</th>
                                        <th class="text-end">MRR</th>
                                        <th class="text-end">ARR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenueByPlan as $planName => $data)
                                        <tr>
                                            <td class="fw-semibold">{{ $planName }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $data['count'] }}</span>
                                            </td>
                                            <td class="text-end text-success fw-semibold">
                                                {{ format_money($data['mrr']) }}
                                            </td>
                                            <td class="text-end text-primary fw-semibold">
                                                {{ format_money($data['arr']) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Billing Cycle Breakdown -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Billing Cycle Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Monthly</div>
                                <div class="fs-3 fw-bold text-primary">{{ number_format($billingCycleStats['monthly']) }}</div>
                                <div class="small text-muted">
                                    {{ $billingCycleStats['total'] > 0 ? round(($billingCycleStats['monthly'] / $billingCycleStats['total']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Yearly</div>
                                <div class="fs-3 fw-bold text-success">{{ number_format($billingCycleStats['yearly']) }}</div>
                                <div class="small text-muted">
                                    {{ $billingCycleStats['total'] > 0 ? round(($billingCycleStats['yearly'] / $billingCycleStats['total']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Churn Details -->
                    <div class="border-top pt-3">
                        <h6 class="text-muted mb-3">Subscription Changes</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Start of Month:</span>
                            <span class="fw-semibold">{{ number_format($churnStats['start_count']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>New Subscriptions:</span>
                            <span class="fw-semibold">+{{ number_format($churnStats['new_count']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Canceled:</span>
                            <span class="fw-semibold">-{{ number_format($churnStats['canceled_count']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="fw-bold">Net Change:</span>
                            <span class="fw-bold {{ ($churnStats['new_count'] - $churnStats['canceled_count']) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $churnStats['new_count'] - $churnStats['canceled_count'] >= 0 ? '+' : '' }}{{ number_format($churnStats['new_count'] - $churnStats['canceled_count']) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Revenue Trend (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    @if(empty($revenueTrend))
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No trend data available</p>
                        </div>
                    @else
                        <canvas id="revenueTrendChart" height="80"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    @if(!empty($recentTransactions))
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Recent Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Tenant</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction['date'] }}</td>
                                        <td>{{ $transaction['tenant'] }}</td>
                                        <td>{{ $transaction['plan'] }}</td>
                                        <td>{{ format_money($transaction['amount']) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction['status'] === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($transaction['status']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    // Revenue Trend Chart
    @if(!empty($revenueTrend))
    const ctx = document.getElementById('revenueTrendChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($revenueTrend)) !!},
                datasets: [{
                    label: 'MRR',
                    data: {!! json_encode(array_column($revenueTrend, 'mrr')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'MRR: {{ format_money(' + context.parsed.y + ') }}';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '{{ format_money(' + value + ') }}';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
</script>
@endpush
