@extends('layouts.client')

@section('title', 'Invoice Details')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('client.invoices.index') }}">Invoices</a></li>
                <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1">Invoice {{ $invoice->invoice_number }}</h2>
        @php
            $statusColors = [
                'draft' => 'secondary',
                'sent' => 'info',
                'paid' => 'success',
                'overdue' => 'danger'
            ];
        @endphp
        <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
            {{ ucfirst($invoice->status) }}
        </span>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('client.invoices.pdf', $invoice) }}" class="btn btn-primary">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
    </div>
</div>

<!-- Invoice Details -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Invoice Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Project:</th>
                        <td>{{ $invoice->project->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Issue Date:</th>
                        <td>{{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Due Date:</th>
                        <td>{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Amount Summary</h5>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Subtotal:</th>
                        <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Tax:</th>
                        <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->tax, 2) }}</td>
                    </tr>
                    <tr class="table-active">
                        <th>Total:</th>
                        <th class="text-end">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Items -->
@if($invoice->items->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Invoice Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end"><strong>{{ $invoice->currency }} {{ number_format($item->total, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th class="text-end">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Tax:</th>
                                <th class="text-end">{{ $invoice->currency }} {{ number_format($invoice->tax, 2) }}</th>
                            </tr>
                            <tr class="table-active">
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-end">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
