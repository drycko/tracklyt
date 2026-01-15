@extends('layouts.app')

@section('title', 'Invoices')
@section('header', 'Invoices')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Invoices</li>
@endsection

@section('actions')
<a href="{{ route('invoices.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>New Invoice
</a>
@endsection

@section('content')
<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('invoices.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select form-select-sm" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="client_id" class="form-label">Client</label>
                <select class="form-select form-select-sm" id="client_id" name="client_id">
                    <option value="">All Clients</option>
                    @foreach(\App\Models\Client::all() as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label">From Date</label>
                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label">To Date</label>
                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Invoices</small>
                        <h3 class="mb-0">{{ \App\Models\Invoice::count() }}</h3>
                    </div>
                    <i class="bi bi-receipt display-6 text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Unpaid</small>
                        <h3 class="mb-0">${{ number_format(\App\Models\Invoice::whereIn('status', ['draft', 'sent', 'overdue'])->sum('total'), 2) }}</h3>
                    </div>
                    <i class="bi bi-clock-history display-6 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Paid (Total)</small>
                        <h3 class="mb-0">${{ number_format(\App\Models\Invoice::paid()->sum('total'), 2) }}</h3>
                    </div>
                    <i class="bi bi-check-circle display-6 text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Overdue</small>
                        <h3 class="mb-0">{{ \App\Models\Invoice::overdue()->count() }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle display-6 text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if($invoices->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Project</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $invoice) }}" class="fw-semibold text-decoration-none">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td>{{ $invoice->client->name }}</td>
                        <td>
                            @if($invoice->project)
                            <a href="{{ route('projects.show', $invoice->project) }}" class="text-decoration-none">
                                {{ $invoice->project->name }}
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td>
                            {{ $invoice->due_date->format('M d, Y') }}
                            @if($invoice->status === 'sent' && $invoice->due_date < now())
                            <i class="bi bi-exclamation-circle text-danger" title="Overdue"></i>
                            @endif
                        </td>
                        <td>
                            <strong>${{ number_format($invoice->total, 2) }}</strong>
                            <small class="text-muted">{{ $invoice->currency }}</small>
                        </td>
                        <td>
                            @if($invoice->status === 'draft')
                            <span class="badge bg-secondary">Draft</span>
                            @elseif($invoice->status === 'sent')
                            <span class="badge bg-primary">Sent</span>
                            @elseif($invoice->status === 'paid')
                            <span class="badge bg-success">Paid</span>
                            @elseif($invoice->status === 'overdue')
                            <span class="badge bg-danger">Overdue</span>
                            @elseif($invoice->status === 'cancelled')
                            <span class="badge bg-dark">Cancelled</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                @if($invoice->canEdit())
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top">
        {{ $invoices->links() }}
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-receipt display-1 text-muted"></i>
        <h4 class="mt-3">No Invoices Found</h4>
        <p class="text-muted">Create your first invoice to start billing clients.</p>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Invoice
        </a>
    </div>
</div>
@endif
@endsection
