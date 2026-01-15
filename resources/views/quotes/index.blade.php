@extends('layouts.app')

@section('title', 'Quotes')
@section('header', 'Quotes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Quotes</li>
@endsection

@section('actions')
<a href="{{ route('quotes.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-circle me-2"></i>New Quote
</a>
@endsection

@section('content')
<!-- Filter Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('quotes.index') }}">
            All Quotes
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'draft' ? 'active' : '' }}" href="{{ route('quotes.index', ['status' => 'draft']) }}">
            <span class="badge bg-secondary">{{ \App\Models\Quote::where('status', 'draft')->count() }}</span> Draft
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'sent' ? 'active' : '' }}" href="{{ route('quotes.index', ['status' => 'sent']) }}">
            <span class="badge bg-info">{{ \App\Models\Quote::where('status', 'sent')->count() }}</span> Sent
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}" href="{{ route('quotes.index', ['status' => 'approved']) }}">
            <span class="badge bg-success">{{ \App\Models\Quote::where('status', 'approved')->count() }}</span> Approved
        </a>
    </li>
</ul>

@if($quotes->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Quote #</th>
                        <th>Client</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Valid Until</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotes as $quote)
                    <tr>
                        <td>
                            <a href="{{ route('quotes.show', $quote) }}" class="text-decoration-none fw-semibold">
                                {{ $quote->quote_number }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('clients.show', $quote->client) }}" class="text-decoration-none">
                                {{ $quote->client->name }}
                            </a>
                        </td>
                        <td>{{ $quote->title }}</td>
                        <td>
                            @if($quote->estimated_cost)
                            <span class="fw-semibold">{{ $quote->currency }} {{ number_format($quote->estimated_cost, 2) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($quote->status === 'draft')
                            <span class="badge bg-secondary">Draft</span>
                            @elseif($quote->status === 'sent')
                            <span class="badge bg-info">Sent</span>
                            @elseif($quote->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                            @elseif($quote->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @else
                            <span class="badge bg-warning">Expired</span>
                            @endif
                        </td>
                        <td>
                            @if($quote->valid_until)
                            {{ $quote->valid_until->format('M d, Y') }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($quote->canEdit())
                                <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" 
                                        onclick="if(confirm('Are you sure you want to delete this quote?')) { document.getElementById('delete-{{ $quote->id }}').submit(); }">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delete-{{ $quote->id }}" action="{{ route('quotes.destroy', $quote) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $quotes->links() }}
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-file-text display-1 text-muted mb-3"></i>
        <h5>No Quotes Found</h5>
        <p class="text-muted mb-4">Get started by creating your first quote for a client.</p>
        <a href="{{ route('quotes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Your First Quote
        </a>
    </div>
</div>
@endif
@endsection
