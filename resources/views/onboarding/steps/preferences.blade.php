<form action="{{ route('onboarding.save-preferences') }}" method="POST">
    @csrf
    
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary mb-2">Workspace Preferences</h4>
        <p class="text-muted">Customize how you work</p>
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
            <label class="form-label fw-semibold">Currency <span class="text-danger">*</span></label>
            <select class="form-select" name="currency" required>
                <option value="ZAR" {{ old('currency', $tenant->currency ?? 'ZAR') === 'ZAR' ? 'selected' : '' }}>ZAR (South African Rand)</option>
                <option value="USD" {{ old('currency', $tenant->currency) === 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                <option value="EUR" {{ old('currency', $tenant->currency) === 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                <option value="GBP" {{ old('currency', $tenant->currency) === 'GBP' ? 'selected' : '' }}>GBP (British Pound)</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Timezone <span class="text-danger">*</span></label>
            <select class="form-select" name="timezone" required>
                <option value="Africa/Johannesburg" {{ old('timezone', $tenant->timezone ?? 'Africa/Johannesburg') === 'Africa/Johannesburg' ? 'selected' : '' }}>Africa/Johannesburg (GMT+2)</option>
                <option value="UTC" {{ old('timezone', $tenant->timezone) === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                <option value="America/New_York" {{ old('timezone', $tenant->timezone) === 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                <option value="America/Los_Angeles" {{ old('timezone', $tenant->timezone) === 'America/Los_Angeles' ? 'selected' : '' }}>America/Los Angeles (PST)</option>
                <option value="Europe/London" {{ old('timezone', $tenant->timezone) === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                <option value="Europe/Paris" {{ old('timezone', $tenant->timezone) === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (CET)</option>
                <option value="Asia/Dubai" {{ old('timezone', $tenant->timezone) === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                <option value="Australia/Sydney" {{ old('timezone', $tenant->timezone) === 'Australia/Sydney' ? 'selected' : '' }}>Australia/Sydney (AEDT)</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Date Format</label>
            <select class="form-select" name="date_format">
                <option value="Y-m-d" {{ old('date_format', $tenant->getSetting('date_format') ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>2025-01-15</option>
                <option value="m/d/Y" {{ old('date_format', $tenant->getSetting('date_format')) === 'm/d/Y' ? 'selected' : '' }}>01/15/2025</option>
                <option value="d/m/Y" {{ old('date_format', $tenant->getSetting('date_format')) === 'd/m/Y' ? 'selected' : '' }}>15/01/2025</option>
                <option value="d-M-Y" {{ old('date_format', $tenant->getSetting('date_format')) === 'd-M-Y' ? 'selected' : '' }}>15-Jan-2025</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Time Format</label>
            <select class="form-select" name="time_format">
                <option value="H:i" {{ old('time_format', $tenant->getSetting('time_format') ?? 'H:i') === 'H:i' ? 'selected' : '' }}>24-hour (14:30)</option>
                <option value="h:i A" {{ old('time_format', $tenant->getSetting('time_format')) === 'h:i A' ? 'selected' : '' }}>12-hour (02:30 PM)</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Week Starts On</label>
            <select class="form-select" name="week_start">
                <option value="monday" {{ old('week_start', $tenant->getSetting('week_start') ?? 'monday') === 'monday' ? 'selected' : '' }}>Monday</option>
                <option value="sunday" {{ old('week_start', $tenant->getSetting('week_start')) === 'sunday' ? 'selected' : '' }}>Sunday</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Language</label>
            <select class="form-select" name="language">
                <option value="en" {{ old('language', $tenant->getSetting('language') ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
            </select>
            <small class="text-muted">More languages coming soon</small>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h6 class="card-title mb-3">Feature Preferences</h6>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="allow_client_portal" id="allow_client_portal" value="1" {{ old('allow_client_portal', false) ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_client_portal">
                    Enable client portal access
                    <small class="d-block text-muted">Allow clients to log in and view their projects/invoices</small>
                </label>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between gap-2 mt-4">
        <a href="{{ route('onboarding.index', ['step' => 3]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-2"></i>Complete Setup
        </button>
    </div>
</form>
