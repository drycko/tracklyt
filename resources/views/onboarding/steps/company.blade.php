<form action="{{ route('onboarding.save-company') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary mb-2">Company Information</h4>
        <p class="text-muted">Tell us about your business</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Workspace Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $tenant->name) }}" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Legal Company Name</label>
            <input type="text" class="form-control" name="company_name" value="{{ old('company_name', $tenant->company_name) }}">
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label fw-semibold">Company Logo</label>
            <input type="file" class="form-control" name="company_logo" accept="image/*">
            <small class="text-muted">PNG, JPG, SVG (max 2MB)</small>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Website</label>
            <input type="url" class="form-control" name="website" value="{{ old('website', $tenant->website) }}" placeholder="https://">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Phone</label>
            <input type="tel" class="form-control" name="phone" value="{{ old('phone', $tenant->phone) }}">
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label fw-semibold">Address</label>
            <textarea class="form-control" name="address" rows="2">{{ old('address', $tenant->address) }}</textarea>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">City</label>
            <input type="text" class="form-control" name="city" value="{{ old('city', $tenant->city) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">State/Province</label>
            <input type="text" class="form-control" name="state" value="{{ old('state', $tenant->state) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Postal Code</label>
            <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code', $tenant->postal_code) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="country" value="{{ old('country', $tenant->country ?? 'South Africa') }}" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Industry</label>
            <input type="text" class="form-control" name="industry" value="{{ old('industry', $tenant->industry) }}" placeholder="e.g., Web Development">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Company Size</label>
            <select class="form-select" name="company_size">
                <option value="">Select...</option>
                @foreach(['1-5', '6-10', '11-25', '26-50', '51-100', '100+'] as $size)
                <option value="{{ $size }}" {{ old('company_size', $tenant->company_size) === $size ? 'selected' : '' }}>
                    {{ $size }} employees
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" class="btn btn-primary">
            Next: Team Setup <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>
</form>
