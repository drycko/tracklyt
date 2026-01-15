@extends('layouts.app')

@section('title', $quote->quote_number)
@section('header', $quote->quote_number)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
<li class="breadcrumb-item active">{{ $quote->quote_number }}</li>
@endsection

@section('actions')
<div class="d-flex gap-2">
    @if($quote->canEdit())
    <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-outline-primary">
        <i class="bi bi-pencil me-2"></i>Edit
    </a>
    @endif
    
    @if($quote->isApproved() && !$quote->project)
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#convertModal">
        <i class="bi bi-arrow-right-circle me-2"></i>Convert to Project
    </button>
    @endif

    <div class="dropdown">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('quotes.pdf', $quote) }}"><i class="bi bi-download me-2"></i>Download PDF</a></li>
            <li>
                <form action="{{ route('quotes.duplicate', $quote) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-files me-2"></i>Duplicate
                    </button>
                </form>
            </li>
            @if($quote->canEdit())
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" 
                   onclick="if(confirm('Are you sure?')) { document.getElementById('delete-form').submit(); }">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
            <form id="delete-form" action="{{ route('quotes.destroy', $quote) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Quote Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quote Details</h5>
                @if($quote->status === 'draft')
                <span class="badge bg-secondary">Draft</span>
                @elseif($quote->status === 'sent')
                <span class="badge bg-info">Sent</span>
                @elseif($quote->status === 'approved')
                <span class="badge bg-success">Approved</span>
                @elseif($quote->status === 'rejected')
                <span class="badge bg-danger">Rejected</span>
                @else
                <span class="badge bg-warning">Expired</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Client</small>
                        <a href="{{ route('clients.show', $quote->client) }}" class="fw-semibold text-decoration-none">
                            {{ $quote->client->name }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Valid Until</small>
                        <span class="fw-semibold">
                            @if($quote->valid_until)
                            {{ $quote->valid_until->format('M d, Y') }}
                            @if($quote->valid_until->isPast())
                            <span class="badge bg-danger ms-2">Expired</span>
                            @endif
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Title</small>
                    <h5 class="mb-0">{{ $quote->title }}</h5>
                </div>

                @if($quote->description)
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <div class="bg-light rounded p-3 markdown-content">
                        {!! markdown($quote->description) !!}
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Estimated Hours</small>
                        <div class="fs-5 fw-semibold">
                            @if($quote->estimated_hours)
                            {{ number_format($quote->estimated_hours, 1) }} hrs
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Estimated Cost</small>
                        <div class="fs-5 fw-semibold text-primary">
                            @if($quote->estimated_cost)
                            {{ $quote->currency }} {{ number_format($quote->estimated_cost, 2) }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Hourly Rate</small>
                        <div class="fs-5 fw-semibold">
                            @if($quote->estimated_hours && $quote->estimated_cost && $quote->estimated_hours > 0)
                            {{ $quote->currency }} {{ number_format($quote->estimated_cost / $quote->estimated_hours, 2) }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Status (if converted) -->
        @if($quote->project)
        <div class="alert alert-success border-0 shadow-sm" style="border-left: 4px solid #198754 !important;">
            <div class="d-flex align-items-start">
                <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>Converted to Project</strong>
                    <p class="mb-0 mt-1">This quote has been converted to 
                        <a href="{{ route('projects.show', $quote->project) }}" class="alert-link">{{ $quote->project->name }}</a>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Needed -->
        @if($quote->status === 'draft')
        <div class="alert alert-info border-0 shadow-sm" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                <div class="flex-grow-1">
                    <strong>Ready to Send?</strong>
                    <p class="mb-2 mt-1">This quote is in draft status. Update the status when ready.</p>
                    <form action="{{ route('quotes.update-status', $quote) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="sent">
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="bi bi-send me-1"></i>Mark as Sent
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @if($quote->status === 'sent')
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Update Status</h6>
            </div>
            <div class="card-body">
                <p class="small mb-3">Change quote status based on client response:</p>
                <form action="{{ route('quotes.update-status', $quote) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select" required>
                        <option value="">Select new status...</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="expired">Expired</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Meta Info -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Quote Number</small>
                    <span class="fw-semibold">{{ $quote->quote_number }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Created By</small>
                    <span>{{ $quote->creator->name }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Created Date</small>
                    <span>{{ $quote->created_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Last Updated</small>
                    <span>{{ $quote->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <!-- Client Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Client Info</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $quote->client->name }}</div>
                        <small class="text-muted">{{ $quote->client->email }}</small>
                    </div>
                </div>
                <a href="{{ route('clients.show', $quote->client) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-eye me-2"></i>View Client Details
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Convert to Project Modal -->
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Convert to Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('quotes.convert', $quote) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>This will create a new project based on this quote. The following will be set:</p>
                    <ul>
                        <li>Client: {{ $quote->client->name }}</li>
                        <li>Project Name: {{ $quote->title }}</li>
                        <li>Budget: {{ $quote->currency }} {{ number_format($quote->estimated_cost, 2) }}</li>
                    </ul>
                    <p class="text-muted small mb-0">You can edit project details after creation.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-arrow-right-circle me-2"></i>Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
