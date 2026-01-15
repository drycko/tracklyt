<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-building text-primary me-2"></i>Company Profile</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.update-company') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Tenant Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="company_name" class="form-label">Legal Company Name</label>
                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                           id="company_name" name="company_name" value="{{ old('company_name', $tenant->company_name) }}">
                    @error('company_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="company_logo" class="form-label">Company Logo</label>
                    @if($tenant->company_logo)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $tenant->company_logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                    @endif
                    <input type="file" class="form-control @error('company_logo') is-invalid @enderror" 
                           id="company_logo" name="company_logo" accept="image/*">
                    <small class="text-muted">PNG, JPG, SVG (max 2MB)</small>
                    @error('company_logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                           id="website" name="website" value="{{ old('website', $tenant->website) }}">
                    @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" value="{{ old('phone', $tenant->phone) }}">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" name="address" rows="2">{{ old('address', $tenant->address) }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                           id="city" name="city" value="{{ old('city', $tenant->city) }}">
                    @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="state" class="form-label">State/Province</label>
                    <input type="text" class="form-control @error('state') is-invalid @enderror" 
                           id="state" name="state" value="{{ old('state', $tenant->state) }}">
                    @error('state')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                           id="postal_code" name="postal_code" value="{{ old('postal_code', $tenant->postal_code) }}">
                    @error('postal_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select @error('country') is-invalid @enderror" 
                            id="country" name="country">
                        <option value="">Select Country</option>
                        @foreach(get_countries_list() as $country)
                        <option value="{{ $country }}" {{ old('country', $tenant->country) === $country ? 'selected' : '' }}>
                            {{ $country }}
                        </option>
                        @endforeach
                    </select>
                    @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="industry" class="form-label">Industry</label>
                    <input type="text" class="form-control @error('industry') is-invalid @enderror" 
                           id="industry" name="industry" value="{{ old('industry', $tenant->industry) }}">
                    @error('industry')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="company_size" class="form-label">Company Size</label>
                    <select class="form-select @error('company_size') is-invalid @enderror" 
                            id="company_size" name="company_size">
                        <option value="">Select...</option>
                        @foreach(['1-5', '6-10', '11-25', '26-50', '51-100', '100+'] as $size)
                        <option value="{{ $size }}" {{ old('company_size', $tenant->company_size) === $size ? 'selected' : '' }}>
                            {{ $size }} employees
                        </option>
                        @endforeach
                    </select>
                    @error('company_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tax_number" class="form-label">Tax/VAT Number</label>
                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                           id="tax_number" name="tax_number" value="{{ old('tax_number', $tenant->tax_number) }}">
                    @error('tax_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Update Company
                </button>
            </div>
        </form>
    </div>
</div>
