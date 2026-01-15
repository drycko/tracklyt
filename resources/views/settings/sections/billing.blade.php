<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-credit-card text-primary me-2"></i>Billing Information</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.update-billing') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="billing_email" class="form-label">Billing Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('billing_email') is-invalid @enderror" 
                           id="billing_email" name="billing_email" value="{{ old('billing_email', $tenant->billing_email) }}" required>
                    @error('billing_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="billing_contact_name" class="form-label">Billing Contact Name</label>
                    <input type="text" class="form-control @error('billing_contact_name') is-invalid @enderror" 
                           id="billing_contact_name" name="billing_contact_name" value="{{ old('billing_contact_name', $tenant->billing_contact_name) }}">
                    @error('billing_contact_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="billing_contact_phone" class="form-label">Billing Contact Phone</label>
                    <input type="tel" class="form-control @error('billing_contact_phone') is-invalid @enderror" 
                           id="billing_contact_phone" name="billing_contact_phone" value="{{ old('billing_contact_phone', $tenant->billing_contact_phone) }}">
                    @error('billing_contact_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                    <select class="form-select @error('currency') is-invalid @enderror" 
                            id="currency" name="currency" required>
                        <option value="ZAR" {{ old('currency', $tenant->currency) === 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                        <option value="USD" {{ old('currency', $tenant->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                        <option value="EUR" {{ old('currency', $tenant->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                        <option value="GBP" {{ old('currency', $tenant->currency) === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                    </select>
                    @error('currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" 
                            id="payment_method" name="payment_method">
                        <option value="">Select...</option>
                        <option value="card" {{ old('payment_method', $tenant->payment_method) === 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                        <option value="eft" {{ old('payment_method', $tenant->payment_method) === 'eft' ? 'selected' : '' }}>EFT/Bank Transfer</option>
                        <option value="manual" {{ old('payment_method', $tenant->payment_method) === 'manual' ? 'selected' : '' }}>Manual/Invoice</option>
                    </select>
                    @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @if($tenant->stripe_customer_id)
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Stripe Integration Active</strong><br>
                <small>Customer ID: {{ $tenant->stripe_customer_id }}</small>
            </div>
            @endif

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Update Billing
                </button>
            </div>
        </form>
    </div>
</div>
