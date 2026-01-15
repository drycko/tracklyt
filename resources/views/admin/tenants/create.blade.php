@extends('admin.layout')

@section('title', 'Create Tenant')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 fw-bold text-dark mb-0">
            Create Tenant
        </h1>
        <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Tenants
        </a>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <form method="POST" action="{{ route('admin.tenants.store') }}">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Tenant Information -->
                        <div class="mb-4">
                            <h5 class="card-title fw-semibold mb-3">Tenant Information</h5>
                            
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-12">
                                    <label for="name" class="form-label fw-medium">Company Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Contact Name -->
                                <div class="col-md-6">
                                    <label for="contact_name" class="form-label fw-medium">Contact Name</label>
                                    <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" required
                                           class="form-control @error('contact_name') is-invalid @enderror">
                                    @error('contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Contact Email -->
                                <div class="col-md-6">
                                    <label for="contact_email" class="form-label fw-medium">Contact Email</label>
                                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}" required
                                           class="form-control @error('contact_email') is-invalid @enderror">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Domain -->
                                <div class="col-12">
                                    <label for="domain" class="form-label fw-medium">Domain <span class="text-muted">(Optional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">https://</span>
                                        <input type="text" name="domain" id="domain" value="{{ old('domain') }}"
                                               class="form-control @error('domain') is-invalid @enderror"
                                               placeholder="example.com">
                                    </div>
                                    @error('domain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-medium">Status</label>
                                    <select name="status" id="status" required
                                            class="form-select @error('status') is-invalid @enderror">
                                        <option value="">Select status</option>
                                        <option value="trial" {{ old('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="canceled" {{ old('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Setup -->
                        <div class="mb-4">
                            <h5 class="card-title fw-semibold mb-3">Subscription Setup</h5>
                            
                            <div class="row g-3">
                                <!-- Subscription Plan -->
                                <div class="col-12">
                                    <label for="plan_id" class="form-label fw-medium">Subscription Plan <span class="text-muted">(Optional)</span></label>
                                    <select name="plan_id" id="plan_id"
                                            class="form-select @error('plan_id') is-invalid @enderror">
                                        <option value="">No subscription plan</option>
                                        @if(isset($plans))
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->name }} - ${{ number_format($plan->price, 2) }}/{{ $plan->billing_period }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="form-text">Leave blank if no subscription is needed initially</div>
                                    @error('plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Tenant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-generate domain suggestion from company name
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const domain = name.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '') // Remove special characters
                .replace(/\s+/g, '') // Remove spaces
                .trim();
            
            if (domain && !document.getElementById('domain').value) {
                document.getElementById('domain').placeholder = domain + '.com';
            }
        });
    </script>
@endsection