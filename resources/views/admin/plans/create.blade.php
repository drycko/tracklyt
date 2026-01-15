@extends('admin.layout')

@section('title', 'Create Subscription Plan')
@section('header', 'Create Subscription Plan')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">Plan Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.plans.store') }}" method="POST">
                    @csrf

                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Basic Information</h6>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug') }}" required>
                            <small class="text-muted">Used in URLs (e.g., starter, professional, enterprise)</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Pricing</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price_monthly" class="form-label">Monthly Price ({{ get_currency_symbol() }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('price_monthly') is-invalid @enderror" 
                                       id="price_monthly" name="price_monthly" value="{{ old('price_monthly', 0) }}" required>
                                @error('price_monthly')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="price_yearly" class="form-label">Yearly Price ({{ get_currency_symbol() }}) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('price_yearly') is-invalid @enderror" 
                                       id="price_yearly" name="price_yearly" value="{{ old('price_yearly', 0) }}" required>
                                @error('price_yearly')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Limits -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Limits <small class="text-muted">(Use -1 for unlimited)</small></h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_users" class="form-label">Max Users <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_users') is-invalid @enderror" 
                                       id="max_users" name="max_users" value="{{ old('max_users', -1) }}" required>
                                @error('max_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_projects" class="form-label">Max Projects <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_projects') is-invalid @enderror" 
                                       id="max_projects" name="max_projects" value="{{ old('max_projects', -1) }}" required>
                                @error('max_projects')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_clients" class="form-label">Max Clients <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_clients') is-invalid @enderror" 
                                       id="max_clients" name="max_clients" value="{{ old('max_clients', -1) }}" required>
                                @error('max_clients')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_monthly_hours" class="form-label">Max Monthly Hours <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_monthly_hours') is-invalid @enderror" 
                                       id="max_monthly_hours" name="max_monthly_hours" value="{{ old('max_monthly_hours', -1) }}" required>
                                @error('max_monthly_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_invoices_per_month" class="form-label">Max Invoices/Month <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_invoices_per_month') is-invalid @enderror" 
                                       id="max_invoices_per_month" name="max_invoices_per_month" value="{{ old('max_invoices_per_month', -1) }}" required>
                                @error('max_invoices_per_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_twilio_messages_per_month" class="form-label">Max Twilio Messages/Month <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_twilio_messages_per_month') is-invalid @enderror" 
                                       id="max_twilio_messages_per_month" name="max_twilio_messages_per_month" value="{{ old('max_twilio_messages_per_month', -1) }}" required>
                                @error('max_twilio_messages_per_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Features</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="time_tracking" name="time_tracking" value="1" {{ old('time_tracking') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="time_tracking">Time Tracking</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="invoicing" name="invoicing" value="1" {{ old('invoicing') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="invoicing">Invoicing</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="client_portal" name="client_portal" value="1" {{ old('client_portal') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="client_portal">Client Portal</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maintenance_reports" name="maintenance_reports" value="1" {{ old('maintenance_reports') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_reports">Maintenance Reports</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="advanced_reporting" name="advanced_reporting" value="1" {{ old('advanced_reporting') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="advanced_reporting">Advanced Reporting</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="api_access" name="api_access" value="1" {{ old('api_access') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="api_access">API Access</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="white_label" name="white_label" value="1" {{ old('white_label') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="white_label">White Label</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="priority_support" name="priority_support" value="1" {{ old('priority_support') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="priority_support">Priority Support</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Display -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Status & Display</h6>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active (Available for subscription)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured (Highlighted on pricing page)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                            <small class="text-muted">Lower numbers appear first</small>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Create Plan
                        </button>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slug = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
