<form action="{{ route('onboarding.save-quick-start') }}" method="POST">
    @csrf
    
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary mb-2">Quick Start Data</h4>
        <p class="text-muted">Create your first client and project (optional)</p>
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

    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="skip_quick_start" name="skip_quick_start" value="1">
        <label class="form-check-label" for="skip_quick_start">
            Skip this step and set up clients/projects later
        </label>
    </div>

    <div id="quick-start-form">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-people me-2 text-primary"></i>First Client
                </h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Client Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="client_name" value="{{ old('client_name') }}" placeholder="Company or Individual Name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" name="client_email" value="{{ old('client_email') }}" placeholder="contact@example.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="tel" class="form-control" name="client_phone" value="{{ old('client_phone') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-folder me-2 text-primary"></i>First Project
                </h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="project_name" value="{{ old('project_name') }}" placeholder="Website Redesign">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" name="project_description" rows="2" placeholder="Brief description of the project...">{{ old('project_description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between gap-2 mt-4">
        <a href="{{ route('onboarding.index', ['step' => 2]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
        <button type="submit" class="btn btn-primary">
            Next: Preferences <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </div>
</form>

<script>
document.getElementById('skip_quick_start').addEventListener('change', function() {
    const form = document.getElementById('quick-start-form');
    const inputs = form.querySelectorAll('input, textarea');
    
    if(this.checked) {
        form.style.opacity = '0.5';
        form.style.pointerEvents = 'none';
        inputs.forEach(input => input.required = false);
    } else {
        form.style.opacity = '1';
        form.style.pointerEvents = 'auto';
        // Re-apply required to essential fields
        document.querySelector('[name="client_name"]').required = true;
        document.querySelector('[name="project_name"]').required = true;
    }
});
</script>
