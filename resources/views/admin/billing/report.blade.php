@extends('admin.layout')

@section('title', 'Detailed Revenue Report')
@section('header', 'Detailed Revenue Report')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Overview
    </a>
    <a href="{{ route('admin.billing.export') }}" class="btn btn-outline-success">
        <i class="bi bi-download me-1"></i>Export Report
    </a>
</div>
@endsection

@section('content')
    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.billing.report') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ request('start_date', now()->subMonths(6)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ request('end_date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter me-1"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <!-- Customer Lifetime Value -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-star fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Customer Lifetime Value</h6>
                            <h3 class="mb-0">{{ format_money($customerLTV ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Revenue Per User -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-person-fill fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Avg Revenue Per User</h6>
                            <h3 class="mb-0">{{ format_money(($customerLTV ?? 0) / 12) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Period -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-calendar-range fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Report Period</h6>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</div>
                            <small class="text-muted">to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Performance -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Plan Performance</h5>
                </div>
                <div class="card-body">
                    @if(empty($planPerformance))
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No performance data available for this period</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Plan Name</th>
                                        <th class="text-center">New Subscriptions</th>
                                        <th class="text-center">Canceled</th>
                                        <th class="text-center">Net Growth</th>
                                        <th class="text-end">Growth Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($planPerformance as $plan)
                                        @php
                                            $growthRate = $plan['new'] > 0 ? (($plan['net_growth'] / $plan['new']) * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $plan['name'] }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">+{{ $plan['new'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">-{{ $plan['canceled'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $plan['net_growth'] >= 0 ? 'success' : 'danger' }}">
                                                    {{ $plan['net_growth'] >= 0 ? '+' : '' }}{{ $plan['net_growth'] }}
                                                </span>
                                            </td>
                                            <td class="text-end {{ $growthRate >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                                {{ number_format($growthRate, 1) }}%
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
    </div>

    <!-- Daily Revenue (if available) -->
    @if(!empty($dailyRevenue))
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Daily Revenue Breakdown</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyRevenueChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Export Options -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">Export Options</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Export this report in various formats for further analysis.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.billing.export', ['format' => 'csv']) }}" class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export as CSV
                        </a>
                        {{-- <a href="{{ route('admin.billing.export', ['format' => 'excel']) }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-excel me-1"></i>Export as Excel
                        </a> --}}
                        <a href="{{ route('admin.billing.export', ['format' => 'pdf']) }}" class="btn btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Export as PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Plan Performance Chart (if data available)
    @if(!empty($planPerformance))
    const planPerformanceData = {!! json_encode($planPerformance) !!};
    
    // You can add Chart.js visualizations here if needed
    @endif

    // Daily Revenue Chart
    @if(!empty($dailyRevenue))
    const dailyCtx = document.getElementById('dailyRevenueChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($dailyRevenue)) !!},
                datasets: [{
                    label: 'Daily Revenue',
                    data: {!! json_encode(array_values($dailyRevenue)) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
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
