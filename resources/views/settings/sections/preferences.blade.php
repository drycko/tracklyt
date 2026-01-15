<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-sliders text-primary me-2"></i>Preferences</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.update-preferences') }}" method="POST">
            @csrf
            @method('PUT')

            <h6 class="mb-3">Regional Settings</h6>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                    <select class="form-select @error('timezone') is-invalid @enderror" 
                            id="timezone" name="timezone" required>
                        <option value="Africa/Johannesburg" {{ old('timezone', $tenant->timezone) === 'Africa/Johannesburg' ? 'selected' : '' }}>Africa/Johannesburg (SAST)</option>
                        <option value="UTC" {{ old('timezone', $tenant->timezone) === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="America/New_York" {{ old('timezone', $tenant->timezone) === 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                        <option value="America/Los_Angeles" {{ old('timezone', $tenant->timezone) === 'America/Los_Angeles' ? 'selected' : '' }}>America/Los Angeles (PST)</option>
                        <option value="Europe/London" {{ old('timezone', $tenant->timezone) === 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                        <option value="Europe/Paris" {{ old('timezone', $tenant->timezone) === 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (CET)</option>
                        <option value="Asia/Dubai" {{ old('timezone', $tenant->timezone) === 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                        <option value="Asia/Singapore" {{ old('timezone', $tenant->timezone) === 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (SGT)</option>
                    </select>
                    @error('timezone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="date_format" class="form-label">Date Format</label>
                    <select class="form-select @error('date_format') is-invalid @enderror" 
                            id="date_format" name="date_format">
                        @php
                            $currentDateFormat = old('date_format', $tenant->getSetting('date_format', 'Y-m-d'));
                        @endphp
                        <option value="Y-m-d" {{ $currentDateFormat === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD ({{ now()->format('Y-m-d') }})</option>
                        <option value="d/m/Y" {{ $currentDateFormat === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY ({{ now()->format('d/m/Y') }})</option>
                        <option value="m/d/Y" {{ $currentDateFormat === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY ({{ now()->format('m/d/Y') }})</option>
                        <option value="d-M-Y" {{ $currentDateFormat === 'd-M-Y' ? 'selected' : '' }}>DD-Mon-YYYY ({{ now()->format('d-M-Y') }})</option>
                    </select>
                    @error('date_format')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="time_format" class="form-label">Time Format</label>
                    <select class="form-select @error('time_format') is-invalid @enderror" 
                            id="time_format" name="time_format">
                        @php
                            $currentTimeFormat = old('time_format', $tenant->getSetting('time_format', '24'));
                        @endphp
                        <option value="24" {{ $currentTimeFormat === '24' ? 'selected' : '' }}>24 Hour ({{ now()->format('H:i') }})</option>
                        <option value="12" {{ $currentTimeFormat === '12' ? 'selected' : '' }}>12 Hour ({{ now()->format('h:i A') }})</option>
                    </select>
                    @error('time_format')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="week_start" class="form-label">Week Starts On</label>
                    <select class="form-select @error('week_start') is-invalid @enderror" 
                            id="week_start" name="week_start">
                        @php
                            $currentWeekStart = old('week_start', $tenant->getSetting('week_start', 'monday'));
                        @endphp
                        <option value="monday" {{ $currentWeekStart === 'monday' ? 'selected' : '' }}>Monday</option>
                        <option value="sunday" {{ $currentWeekStart === 'sunday' ? 'selected' : '' }}>Sunday</option>
                    </select>
                    @error('week_start')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <h6 class="mb-3">Feature Access</h6>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="allow_client_portal" value="1" 
                       id="allow_client_portal" {{ old('allow_client_portal', $tenant->allow_client_portal) ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_client_portal">
                    <strong>Enable Client Portal</strong>
                    <br><small class="text-muted">Allow clients to access their projects, invoices, and reports</small>
                </label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="allow_api_access" value="1" 
                       id="allow_api_access" {{ old('allow_api_access', $tenant->allow_api_access) ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_api_access">
                    <strong>Enable API Access</strong>
                    <br><small class="text-muted">Generate API tokens for third-party integrations</small>
                </label>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Update Preferences
                </button>
            </div>
        </form>
    </div>
</div>
