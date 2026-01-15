@extends('layouts.client')

@section('title', 'Quote Details')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('client.quotes.index') }}">Quotes</a></li>
                <li class="breadcrumb-item active">{{ $quote->quote_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1">{{ $quote->title }}</h2>
        <p class="text-muted">Quote #{{ $quote->quote_number }}</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="{{ route('client.quotes.pdf', $quote) }}" class="btn btn-primary">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
    </div>
</div>

<!-- Quote Details -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Quote Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Status:</th>
                        <td>
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'sent' => 'info',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'expired' => 'warning'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$quote->status] ?? 'secondary' }}">
                                {{ ucfirst($quote->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Estimated Hours:</th>
                        <td>{{ $quote->estimated_hours }} hours</td>
                    </tr>
                    <tr>
                        <th>Estimated Cost:</th>
                        <td><strong>{{ $quote->currency }} {{ number_format($quote->estimated_cost, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Valid Until:</th>
                        <td>{{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Description</h5>
                <p>{{ $quote->description ?? 'No description provided.' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Line Items -->
@if($quote->lineItems->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Line Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th class="text-end">Hours</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quote->lineItems as $item)
                            <tr>
                                <td><span class="badge bg-secondary">{{ ucfirst($item->category) }}</span></td>
                                <td>{{ $item->description }}</td>
                                <td class="text-end">{{ $item->hours }}</td>
                                <td class="text-end">{{ $quote->currency }} {{ number_format($item->rate, 2) }}</td>
                                <td class="text-end"><strong>{{ $quote->currency }} {{ number_format($item->total, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th class="text-end">{{ $quote->currency }} {{ number_format($quote->estimated_cost, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tech Stack -->
@if($quote->techStack)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Technical Stack</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Language:</strong><br>
                        {{ $quote->techStack->language ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Framework:</strong><br>
                        {{ $quote->techStack->framework ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Database:</strong><br>
                        {{ $quote->techStack->database ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Hosting:</strong><br>
                        {{ $quote->techStack->hosting ?? 'N/A' }}
                    </div>
                </div>
                @if($quote->techStack->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Notes:</strong><br>
                        <p class="mb-0">{{ $quote->techStack->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
