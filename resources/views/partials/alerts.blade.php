@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid #198754 !important;">
    <div class="d-flex align-items-start">
        <i class="bi bi-check-circle-fill me-3 fs-5"></i>
        <div class="flex-grow-1">
            {{ session('success') }}
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid #dc3545 !important;">
    <div class="d-flex align-items-start">
        <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>
        <div class="flex-grow-1">
            {{ session('error') }}
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid #ffc107 !important;">
    <div class="d-flex align-items-start">
        <i class="bi bi-exclamation-circle-fill me-3 fs-5"></i>
        <div class="flex-grow-1">
            {{ session('warning') }}
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid #0dcaf0 !important;">
    <div class="d-flex align-items-start">
        <i class="bi bi-info-circle-fill me-3 fs-5"></i>
        <div class="flex-grow-1">
            {{ session('info') }}
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-left: 4px solid #dc3545 !important;">
    <div class="d-flex align-items-start">
        <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>
        <div class="flex-grow-1">
            <strong class="d-block mb-2">Whoops! There were some problems with your input:</strong>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
