@extends('layouts.app')

@section('title', 'Invoice Details')
@section('header', 'Invoice ' . $invoice->invoice_number)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
<li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-secondary">
        <i class="bi bi-file-pdf me-2"></i>Download PDF
    </a>
    
    @if($invoice->status === 'draft')
    <form action="{{ route('invoices.mark-sent', $invoice) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-send me-2"></i>Mark as Sent
        </button>
    </form>
    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary">
        <i class="bi bi-pencil me-2"></i>Edit
    </a>
    @endif

    @if($invoice->balance_due > 0 && $invoice->status !== 'draft')
    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
        <i class="bi bi-plus-circle me-1"></i>Add Payment
    </button>
    @endif
    
    @if(in_array($invoice->status, ['sent', 'overdue']))
    <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-2"></i>Mark as Paid
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Invoice Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $invoice->invoice_number }}</h4>
                    @if($invoice->status === 'draft')
                    <span class="badge bg-secondary fs-6">Draft</span>
                    @elseif($invoice->status === 'sent')
                    <span class="badge bg-primary fs-6">Sent</span>
                    @elseif($invoice->status === 'paid')
                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Paid</span>
                    @elseif($invoice->status === 'overdue')
                    <span class="badge bg-danger fs-6">Overdue</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Bill To:</h6>
                        <h5 class="mb-1">{{ $invoice->client->name }}</h5>
                        @if($invoice->client->email)
                        <p class="mb-0"><i class="bi bi-envelope me-2"></i>{{ $invoice->client->email }}</p>
                        @endif
                        @if($invoice->client->phone)
                        <p class="mb-0"><i class="bi bi-telephone me-2"></i>{{ $invoice->client->phone }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1"><strong>Issue Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}</p>
                        <p class="mb-1"><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                        @if($invoice->project)
                        <p class="mb-1">
                            <strong>Project:</strong> 
                            <a href="{{ route('projects.show', $invoice->project) }}">{{ $invoice->project->name }}</a>
                        </p>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>
                                    {{ $item->description }}
                                    @if($item->timeEntry)
                                    <br><small class="text-muted">
                                        <i class="bi bi-clock"></i> Time Entry
                                    </small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ format_money($item->unit_price, $invoice->currency, false) }}</td>
                                <td class="text-end">{{ format_money($item->total, $invoice->currency, false) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end">{{ format_money($invoice->subtotal, $invoice->currency) }}</td>
                            </tr>
                            @if($invoice->tax > 0)
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end">{{ format_money($invoice->tax, $invoice->currency) }}</td>
                            </tr>
                            @endif
                            <tr class="table-light">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong class="text-primary fs-5">{{ format_money($invoice->total, $invoice->currency) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($invoice->notes)
                <div class="mt-4">
                    <h6 class="text-muted">Notes:</h6>
                    <p class="mb-0">{{ $invoice->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Payments -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Payments</h6>
                @if($invoice->balance_due > 0 && $invoice->status !== 'draft')
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Payment
                </button>
                @endif
            </div>
            <div class="card-body">
                @if($invoice->payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Method</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>
                                    {{ $payment->payment_method_label }}
                                    @if($payment->reference_number)
                                    <br><small class="text-muted">Ref: {{ $payment->reference_number }}</small>
                                    @endif
                                </td>
                                <td class="text-end text-success fw-semibold">{{ format_money($payment->amount) }}</td>
                                <td class="text-end">
                                    <form action="{{ route('invoices.payments.destroy', [$invoice, $payment]) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @if($payment->notes)
                            <tr>
                                <td colspan="4" class="text-muted small">
                                    <i class="bi bi-sticky me-1"></i>{{ $payment->notes }}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end"><strong>Total Paid:</strong></td>
                                <td class="text-end text-success fw-bold">{{ format_money($invoice->total_paid, $invoice->currency) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-end"><strong>Balance Due:</strong></td>
                                <td class="text-end {{ $invoice->balance_due > 0 ? 'text-warning' : 'text-muted' }} fw-bold">
                                    {{ format_money($invoice->balance_due, $invoice->currency) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-cash-stack text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2 mb-0">No payments recorded yet</p>
                    @if($invoice->status !== 'draft')
                    <button type="button" class="btn btn-sm btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="bi bi-plus-circle me-1"></i>Record First Payment
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Invoice Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">Invoice Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <div class="fw-semibold">
                        @if($invoice->status === 'draft')
                        <span class="badge bg-secondary">Draft</span>
                        @elseif($invoice->status === 'sent')
                        <span class="badge bg-primary">Sent</span>
                        @elseif($invoice->status === 'paid')
                        <span class="badge bg-success">Paid</span>
                        @elseif($invoice->status === 'overdue')
                        <span class="badge bg-danger">Overdue</span>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Amount</small>
                    <div class="fw-semibold fs-4 text-primary">{{ format_money($invoice->total, $invoice->currency) }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Paid</small>
                    <div class="fw-semibold fs-5 text-success">{{ format_money($invoice->total_paid, $invoice->currency) }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Balance Due</small>
                    <div class="fw-semibold fs-5 {{ $invoice->balance_due > 0 ? 'text-warning' : 'text-muted' }}">
                        {{ format_money($invoice->balance_due, $invoice->currency) }}
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Payment Status</small>
                    <div>
                        @if($invoice->payment_status === 'unpaid')
                        <span class="badge bg-secondary">Unpaid</span>
                        @elseif($invoice->payment_status === 'partially_paid')
                        <span class="badge bg-warning">Partially Paid</span>
                        @elseif($invoice->payment_status === 'paid')
                        <span class="badge bg-success">Fully Paid</span>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Line Items</small>
                    <div class="fw-semibold">{{ $invoice->items->count() }}</div>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <div>{{ $invoice->created_at->format('M d, Y g:i A') }}</div>
                </div>
                @if($invoice->status === 'sent' && $invoice->due_date < now())
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Overdue!</strong><br>
                    Due {{ $invoice->due_date->diffForHumans() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-file-pdf me-2"></i>Download PDF
                </a>
                <a href="{{ route('clients.show', $invoice->client) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-person me-2"></i>View Client
                </a>
                @if($invoice->project)
                <a href="{{ route('projects.show', $invoice->project) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-folder me-2"></i>View Project
                </a>
                @endif
                @if($invoice->canEdit())
                <a href="{{ route('invoices.edit', $invoice) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-pencil me-2"></i>Edit Invoice
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('invoices.payments.store', $invoice) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Invoice Total:</strong> {{ format_money($invoice->total, $invoice->currency) }}<br>
                        <strong>Already Paid:</strong> {{ format_money($invoice->total_paid, $invoice->currency) }}<br>
                        <strong>Balance Due:</strong> {{ format_money($invoice->balance_due, $invoice->currency) }}
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                               id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">{{ get_currency_symbol($invoice->currency) }}</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" step="0.01" min="0.01" 
                                   max="{{ $invoice->balance_due }}" 
                                   value="{{ old('amount', $invoice->balance_due) }}" required>
                            @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">Maximum: {{ format_money($invoice->balance_due, $invoice->currency) }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" 
                                id="payment_method" name="payment_method" required>
                            <option value="">Select Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                            <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                               id="reference_number" name="reference_number" value="{{ old('reference_number') }}"
                               placeholder="Transaction ID, Check Number, etc.">
                        @error('reference_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var addPaymentModal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
        addPaymentModal.show();
    });
</script>
@endif
@endsection
