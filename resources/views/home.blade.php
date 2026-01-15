@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- Stats Row -->
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
                        <h3 class="mb-0">{{ $totalClients ?? 0 }}</h3>
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
                        <h3 class="mb-0">{{ $activeProjects ?? 0 }}</h3>
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
                        <h3 class="mb-0">{{ number_format($hoursThisMonth ?? 0, 1) }}</h3>
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
                        <i class="bi bi-receipt fs-2 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pending Invoices</h6>
                        <h3 class="mb-0">{{ $pendingInvoices ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Banner -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white py-4">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="mb-1"><i class="bi bi-currency-dollar fs-1 text-primary"></i></div>
                        <h4 class="mb-0">{{ format_money($revenueThisMonth ?? 0, user_tenant()->currency ?? 'USD') }}</h4>
                        <small class="opacity-75">Revenue This Month</small>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1"><i class="bi bi-wallet2 fs-1 text-warning"></i></div>
                        <h4 class="mb-0">{{ format_money($outstandingAmount ?? 0, user_tenant()->currency ?? 'USD') }}</h4>
                        <small class="opacity-75">Outstanding</small>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1"><i class="bi bi-clock-fill fs-1 text-info"></i></div>
                        <h4 class="mb-0">{{ number_format($billableHoursThisMonth ?? 0, 1) }}h</h4>
                        <small class="opacity-75">Billable Hours</small>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-1"><i class="bi bi-bar-chart-fill fs-1 text-secondary"></i></div>
                        <a href="{{ route('reports.index') }}" class="btn btn-light mt-2">
                            <i class="bi bi-graph-up me-2"></i>View Full Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Running Timer & Recent Activity -->
<div class="row g-4">
    <div class="col-lg-8">
        <!-- Running Timer Card -->
        @php
            $runningTimer = \App\Models\TimeEntry::where('user_id', auth()->id())->running()->first();
        @endphp
        @if($runningTimer)
        <div class="card border-0 shadow-sm mb-4 border-success" style="border-width: 2px !important;">
            <div class="card-header bg-success bg-opacity-10 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-success">
                        <i class="bi bi-play-circle-fill me-2"></i>Active Timer
                    </h5>
                    <span class="badge bg-success">
                        <span class="spinner-grow spinner-grow-sm me-1" role="status" aria-hidden="true"></span>
                        Running
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="mb-2">
                            <small class="text-muted d-block">Project</small>
                            <a href="{{ route('projects.show', $runningTimer->project) }}" class="fw-semibold text-decoration-none">
                                {{ $runningTimer->project->name }}
                            </a>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Task</small>
                            <a href="{{ route('tasks.show', $runningTimer->task) }}" class="fw-semibold text-decoration-none">
                                {{ $runningTimer->task->name }}
                            </a>
                        </div>
                        @if($runningTimer->description)
                        <div class="mb-0">
                            <small class="text-muted d-block">Description</small>
                            <div class="small">{{ $runningTimer->description }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <small class="text-muted d-block">Duration</small>
                            <h2 class="mb-0 text-success" id="dashboardTimerDisplay">
                                {{ gmdate('H:i:s', (now()->timestamp - $runningTimer->start_time->timestamp)) }}
                            </h2>
                        </div>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('time-entries.show', $runningTimer) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <form action="{{ route('time-entries.stop', $runningTimer) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-stop-circle"></i> Stop
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Overdue Invoices Alert -->
        @if(($overdueInvoices ?? collect())->count() > 0)
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Overdue Invoices</h5>
            <p class="mb-2">You have {{ $overdueInvoices->count() }} overdue invoice(s):</p>
            <ul class="mb-0">
                @foreach($overdueInvoices->take(3) as $invoice)
                <li>
                    <a href="{{ route('invoices.show', $invoice) }}" class="alert-link">
                        {{ $invoice->invoice_number }}
                    </a> - {{ $invoice->client->name }} ({{ format_money($invoice->balance_due) }}) - 
                    Due {{ $invoice->due_date->diffForHumans() }}
                </li>
                @endforeach
            </ul>
            @if($overdueInvoices->count() > 3)
            <a href="{{ route('invoices.index') }}?status=overdue" class="alert-link d-block mt-2">
                View {{ $overdueInvoices->count() - 3 }} more...
            </a>
            @endif
        </div>
        @endif

        <!-- Recent Time Entries -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Time Entries</h5>
                    <a href="{{ route('time-entries.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                @php
                    $recentEntries = \App\Models\TimeEntry::with('project', 'task', 'user')
                        ->where('user_id', auth()->id())
                        ->latest('start_time')
                        ->take(5)
                        ->get();
                @endphp
                @if($recentEntries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Task</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEntries as $entry)
                            <tr onclick="window.location='{{ route('time-entries.show', $entry) }}'" style="cursor: pointer;">
                                <td>
                                    <div class="fw-semibold">{{ $entry->start_time->format('M d') }}</div>
                                    <small class="text-muted">{{ $entry->start_time->format('g:i A') }}</small>
                                </td>
                                <td>{{ $entry->project->name }}</td>
                                <td>{{ $entry->task->name }}</td>
                                <td>
                                    <strong>{{ number_format($entry->duration_hours, 2) }}h</strong>
                                    @if($entry->is_billable)
                                    <i class="bi bi-currency-dollar text-success" title="Billable"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($entry->isLocked())
                                    <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i></span>
                                    @elseif($entry->isRunning())
                                    <span class="badge bg-primary"><i class="bi bi-play-fill"></i></span>
                                    @else
                                    <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-clock-history fs-1 mb-3 d-block"></i>
                    <p>No time entries yet. Start tracking your time!</p>
                    <a href="{{ route('time-entries.create') }}" class="btn btn-success">
                        <i class="bi bi-play-circle me-2"></i>Start Timer
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('time-entries.create') }}" class="btn btn-success text-start">
                        <i class="bi bi-play-circle me-2"></i>Start Timer
                    </a>
                    <a href="{{ route('clients.create') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-person-plus me-2"></i>New Client
                    </a>
                    <a href="{{ route('projects.create') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-folder-plus me-2"></i>New Project
                    </a>
                    <a href="{{ route('invoices.create') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-receipt me-2"></i>New Invoice
                    </a>
                    <a href="{{ route('quotes.create') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-file-text me-2"></i>New Quote
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Quotes -->
        @if(($recentQuotes ?? collect())->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Recent Quotes</h6>
                <a href="{{ route('quotes.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach($recentQuotes as $quote)
                <a href="{{ route('quotes.show', $quote) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $quote->quote_number }}</div>
                            <small class="text-muted">{{ $quote->client->name }}</small>
                        </div>
                        <span class="badge bg-{{ $quote->status === 'accepted' ? 'success' : ($quote->status === 'rejected' ? 'danger' : 'secondary') }}">
                            {{ ucfirst($quote->status) }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if(auth()->user()->isCentralUser())
        <div class="card border-0 shadow-sm mt-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-shield-exclamation me-2"></i>Super Admin</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">You have platform-wide access</p>
                <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-danger">
                    Manage Tenants
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Time Chart
const timeCtx = document.getElementById('timeChart');
new Chart(timeCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($monthlyTimeData ?? [], 'month')) !!},
        datasets: [{
            label: 'Hours Tracked',
            data: {!! json_encode(array_column($monthlyTimeData ?? [], 'hours')) !!},
            backgroundColor: 'rgba(255, 193, 7, 0.5)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1
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
        labels: {!! json_encode(array_column($monthlyRevenueData ?? [], 'month')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode(array_column($monthlyRevenueData ?? [], 'revenue')) !!},
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

@if($runningTimer ?? false)
// Update dashboard timer display every second
const startTime = new Date('{{ $runningTimer->start_time->toIso8601String() }}');
const timerDisplay = document.getElementById('dashboardTimerDisplay');

function updateDashboardTimer() {
    const now = new Date();
    const diff = Math.floor((now - startTime) / 1000);
    const hours = Math.floor(diff / 3600).toString().padStart(2, '0');
    const minutes = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
    const seconds = (diff % 60).toString().padStart(2, '0');
    timerDisplay.textContent = `${hours}:${minutes}:${seconds}`;
}

updateDashboardTimer();
setInterval(updateDashboardTimer, 1000);
@endif
</script>
@endsection
