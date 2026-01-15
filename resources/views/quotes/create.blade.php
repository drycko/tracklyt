@extends('layouts.app')

@section('title', 'Create Quote')
@section('header', 'Create Quote')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Quote Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('quotes.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Select a client...</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $preselectedClientId) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Who is this quote for?</small>
                        </div>

                        <div class="col-md-6">
                            <label for="valid_until" class="form-label">Valid Until</label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" value="{{ old('valid_until') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Quote expiration date</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required
                               placeholder="e.g., E-commerce Website Development">
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="5" 
                                  placeholder="Detailed scope of work, deliverables, and technical requirements...">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="estimated_hours" class="form-label">Estimated Hours</label>
                            <input type="number" step="0.5" min="0" 
                                   class="form-control @error('estimated_hours') is-invalid @enderror" 
                                   id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours') }}"
                                   placeholder="0.00">
                            @error('estimated_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="estimated_cost" class="form-label">Estimated Cost</label>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('estimated_cost') is-invalid @enderror" 
                                   id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost') }}"
                                   placeholder="0.00">
                            @error('estimated_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                            <select class="form-select @error('currency') is-invalid @enderror" 
                                    id="currency" name="currency" required>
                                <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="ZAR" {{ old('currency') == 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                            </select>
                            @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary bg-opacity-10 border-bottom">
                <h6 class="mb-0 text-primary"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0 ps-3">
                    <li class="mb-2"><strong>Be Specific:</strong> Clear scope prevents scope creep</li>
                    <li class="mb-2"><strong>Include Tech Stack:</strong> List frameworks and tools</li>
                    <li class="mb-2"><strong>Set Expectations:</strong> Timeline and deliverables</li>
                    <li class="mb-2"><strong>Add Buffer:</strong> Include 10-20% for unforeseen work</li>
                    <li><strong>Valid Until:</strong> Standard is 30 days</li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info bg-opacity-10 border-bottom">
                <h6 class="mb-0 text-info"><i class="bi bi-info-circle me-2"></i>Next Steps</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">After creating the quote:</p>
                <ol class="small mb-0 ps-3">
                    <li class="mb-1">Add line items and tech stack</li>
                    <li class="mb-1">Review and send to client</li>
                    <li class="mb-1">Upon approval, convert to project</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
