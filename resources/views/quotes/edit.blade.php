@extends('layouts.app')

@section('title', 'Edit Quote')
@section('header', 'Edit Quote')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
<li class="breadcrumb-item"><a href="{{ route('quotes.show', $quote) }}">{{ $quote->quote_number }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Edit Quote: {{ $quote->quote_number }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('quotes.update', $quote) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Select a client...</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $quote->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="valid_until" class="form-label">Valid Until</label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" 
                                   value="{{ old('valid_until', $quote->valid_until?->format('Y-m-d')) }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $quote->title) }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="5">{{ old('description', $quote->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="estimated_hours" class="form-label">Estimated Hours</label>
                            <input type="number" step="0.5" min="0" 
                                   class="form-control @error('estimated_hours') is-invalid @enderror" 
                                   id="estimated_hours" name="estimated_hours" 
                                   value="{{ old('estimated_hours', $quote->estimated_hours) }}">
                            @error('estimated_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="estimated_cost" class="form-label">Estimated Cost</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('estimated_cost') is-invalid @enderror" 
                                   id="estimated_cost" name="estimated_cost" 
                                   value="{{ old('estimated_cost', $quote->estimated_cost) }}">
                            @error('estimated_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                            <select class="form-select @error('currency') is-invalid @enderror" 
                                    id="currency" name="currency" required>
                                <option value="USD" {{ old('currency', $quote->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $quote->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency', $quote->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="ZAR" {{ old('currency', $quote->currency) == 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                            </select>
                            @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-warning bg-opacity-10 border-warning">
            <div class="card-body">
                <h6 class="text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Important</h6>
                <p class="small mb-0">Only draft quotes can be edited. Once sent or approved, quotes become read-only.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Quote Status</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Current Status:</strong>
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
                </div>
                <div class="mb-2">
                    <strong>Created:</strong> {{ $quote->created_at->format('M d, Y') }}
                </div>
                <div>
                    <strong>Created By:</strong> {{ $quote->creator->name }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
