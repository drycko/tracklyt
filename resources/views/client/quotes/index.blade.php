@extends('layouts.client')

@section('title', 'My Quotes')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold mb-1">My Quotes</h2>
        <p class="text-muted">View all your project quotes and proposals</p>
    </div>
</div>

@if($quotes->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Quote #</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Estimated Cost</th>
                                <th>Valid Until</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                            <tr>
                                <td><strong>{{ $quote->quote_number }}</strong></td>
                                <td>{{ $quote->title }}</td>
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
                                <td>{{ $quote->currency }} {{ number_format($quote->estimated_cost, 2) }}</td>
                                <td>{{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('client.quotes.show', $quote) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('client.quotes.pdf', $quote) }}" 
                                           class="btn btn-outline-secondary" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            {{ $quotes->links() }}
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-earmark-text text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3">No Quotes Found</h5>
                <p class="text-muted">You don't have any quotes yet.</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
