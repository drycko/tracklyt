@extends('layouts.app')

@section('title', 'Stats & Reports')
@section('header', 'Stats & Reports')

@section('content')
<!-- Summary Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-people fs-2 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Clients</h6>
                        <h3 class="mb-0">{{ $totalClients }}</h3>
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
                        <i class="bi bi-kanban fs-2 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Active Projects</h6>
                        <h3 class="mb-0">{{ $activeProjects }}</h3>
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
                        <i class="bi bi-clock-history fs-2 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Hours This Month</h6>
                        <h3 class="mb-0">{{ number_format($hoursThisMonth, 1) }}</h3>
                        @if($hoursLastMonth > 0)
                        <small class="text-muted">
                            @if($hoursThisMonth > $hoursLastMonth)
                            <i class="bi bi-arrow-up text-success"></i> {{ number_format((($hoursThisMonth - $hoursLastMonth) / $hoursLastMonth) * 100, 1) }}%
                            @else
                            <i class="bi bi-arrow-down text-danger"></i> {{ number_format((($hoursLastMonth - $hoursThisMonth) / $hoursLastMonth) * 100, 1) }}%
                            @endif
                        </small>
                        @endif
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
                        <i class="bi bi-currency-dollar fs-2 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Revenue</h6>
                        <h3 class="mb-0">{{ format_money($totalRevenue, $tenant->currency) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue & Hours Detail -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3">Revenue This Month</h6>
                <h2 class="mb-2">{{ format_money($revenueThisMonth, $tenant->currency) }}</h2>
                @if($revenueLastMonth > 0)
                <small class="text-muted">
                    vs. {{ format_money($revenueLastMonth, $tenant->currency) }} last month
                    @if($revenueThisMonth > $revenueLastMonth)
                    <span class="text-success"><i class="bi bi-arrow-up"></i> +{{ number_format((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) }}%</span>
                    @elseif($revenueThisMonth < $revenueLastMonth)
                    <span class="text-danger"><i class="bi bi-arrow-down"></i> {{ number_format((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) }}%</span>
                    @endif
                </small>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3">Outstanding Amount</h6>
                <h2 class="mb-2 text-warning">{{ format_money($outstandingAmount, $tenant->currency) }}</h2>
                <small class="text-muted">From {{ $pendingInvoices }} pending invoices</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-3">Billable vs Non-Billable</h6>
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-grow-1">
                        <div class="progress" style="height: 25px;">
                            @php
                                $totalHours = $billableHoursThisMonth + $nonBillableHoursThisMonth;
                                $billablePercent = $totalHours > 0 ? ($billableHoursThisMonth / $totalHours) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $billablePercent }}%">
                                {{ number_format($billablePercent, 0) }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between small">
                    <span><i class="bi bi-circle-fill text-success"></i> Billable: {{ number_format($billableHoursThisMonth, 1) }}h</span>
                    <span><i class="bi bi-circle-fill text-secondary"></i> Non-billable: {{ number_format($nonBillableHoursThisMonth, 1) }}h</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Time Tracking Trend (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="timeChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Revenue Trend (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Project & Client Stats -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Projects by Hours</h5>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($projectStats->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Project</th>
                                <th>Client</th>
                                <th class="text-end">Hours</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projectStats->take(10) as $stat)
                            <tr onclick="window.location='{{ route('projects.show', $stat['id']) }}'" style="cursor: pointer;">
                                <td class="fw-semibold">{{ $stat['name'] }}</td>
                                <td>{{ $stat['client'] }}</td>
                                <td class="text-end">{{ $stat['hours'] }}h</td>
                                <td class="text-center">
                                    @if($stat['is_active'])
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-folder display-4"></i>
                    <p class="mt-2">No time tracked yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Client Profitability</h5>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($clientStats->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th class="text-end">Projects</th>
                                <th class="text-end">Hours</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientStats->take(10) as $stat)
                            <tr onclick="window.location='{{ route('clients.show', $stat['id']) }}'" style="cursor: pointer;">
                                <td class="fw-semibold">{{ $stat['name'] }}</td>
                                <td class="text-end">{{ $stat['projects_count'] }}</td>
                                <td class="text-end">{{ $stat['hours'] }}h</td>
                                <td class="text-end text-success">{{ format_money($stat['revenue']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total:</td>
                                <td class="text-end fw-bold text-success">{{ format_money($clientStats->sum('revenue')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people display-4"></i>
                    <p class="mt-2">No client data yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Retainer Usage -->
@if($activeRetainers > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Retainer Usage Overview</h5>
                <a href="{{ route('maintenance-profiles.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($retainerProfiles as $profile)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="mb-0">{{ $profile->project->name }}</h6>
                                    <span class="badge bg-{{ $profile->usage_percentage > 90 ? 'danger' : ($profile->usage_percentage > 75 ? 'warning' : 'success') }}">
                                        {{ number_format($profile->usage_percentage, 0) }}%
                                    </span>
                                </div>
                                <small class="text-muted d-block mb-2">{{ $profile->project->client->name }}</small>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar {{ $profile->usage_percentage > 90 ? 'bg-danger' : ($profile->usage_percentage > 75 ? 'bg-warning' : 'bg-success') }}" 
                                         role="progressbar" 
                                         style="width: {{ min(100, $profile->usage_percentage) }}%">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span>{{ number_format($profile->used_hours, 1) }}h used</span>
                                    <span>{{ number_format($profile->remaining_hours, 1) }}h left</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Team Productivity -->
@if(auth()->user()->isAdmin() && $userStats->count() > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Team Productivity</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Team Member</th>
                                <th class="text-end">Total Hours</th>
                                <th class="text-end">Billable Hours</th>
                                <th class="text-end">Non-Billable Hours</th>
                                <th class="text-end">Billable %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userStats as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user['name'] }}</td>
                                <td class="text-end">{{ $user['total_hours'] }}h</td>
                                <td class="text-end text-success">{{ $user['billable_hours'] }}h</td>
                                <td class="text-end text-secondary">{{ $user['non_billable_hours'] }}h</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $user['billable_percentage'] > 75 ? 'success' : ($user['billable_percentage'] > 50 ? 'warning' : 'danger') }}">
                                        {{ $user['billable_percentage'] }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td class="fw-bold">Total</td>
                                <td class="text-end fw-bold">{{ number_format($userStats->sum('total_hours'), 1) }}h</td>
                                <td class="text-end fw-bold text-success">{{ number_format($userStats->sum('billable_hours'), 1) }}h</td>
                                <td class="text-end fw-bold text-secondary">{{ number_format($userStats->sum('non_billable_hours'), 1) }}h</td>
                                <td class="text-end fw-bold">
                                    @php
                                        $totalHours = $userStats->sum('total_hours');
                                        $avgBillable = $totalHours > 0 ? ($userStats->sum('billable_hours') / $totalHours) * 100 : 0;
                                    @endphp
                                    <span class="badge bg-{{ $avgBillable > 75 ? 'success' : ($avgBillable > 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($avgBillable, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Time Chart with billable/non-billable breakdown
const timeCtx = document.getElementById('timeChart');
new Chart(timeCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($monthlyTimeData, 'month')) !!},
        datasets: [
            {
                label: 'Billable Hours',
                data: {!! json_encode(array_column($monthlyTimeData, 'billable')) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            },
            {
                label: 'Non-Billable Hours',
                data: {!! json_encode(array_column($monthlyTimeData, 'non_billable')) !!},
                backgroundColor: 'rgba(108, 117, 125, 0.5)',
                borderColor: 'rgba(108, 117, 125, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true,
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + 'h';
                    }
                }
            }
        }
    }
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyRevenueData, 'month')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode(array_column($monthlyRevenueData, 'revenue')) !!},
            backgroundColor: 'rgba(23, 162, 184, 0.1)',
            borderColor: 'rgba(23, 162, 184, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
