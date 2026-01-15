@extends('layouts.client')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-4">
            <h2 class="fw-bold mb-1">Welcome back, {{ $client->name }}! 👋</h2>
            <p class="text-muted">Here's an overview of your projects and activity</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Quotes</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['quotes'] }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Projects</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['projects'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-briefcase fs-4 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Active Projects</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['active_projects'] }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="bi bi-play-circle fs-4 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total Invoices</p>
                        <h3 class="mb-0 fw-bold">{{ $stats['invoices'] }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-receipt fs-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Quotes
                </h5>
                <p class="card-text text-muted">View all your project quotes and proposals</p>
                <a href="{{ route('client.quotes.index') }}" class="btn btn-outline-primary">
                    View Quotes <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-briefcase text-success me-2"></i>Projects
                </h5>
                <p class="card-text text-muted">Access your project details, links, and repositories</p>
                <a href="{{ route('client.projects.index') }}" class="btn btn-outline-success">
                    View Projects <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-receipt text-warning me-2"></i>Invoices
                </h5>
                <p class="card-text text-muted">Review and download your invoices</p>
                <a href="{{ route('client.invoices.index') }}" class="btn btn-outline-warning">
                    View Invoices <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contact Info -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="mb-2">Need Help?</h6>
                <p class="mb-0 text-muted">
                    <i class="bi bi-info-circle me-2"></i>
                    If you have any questions about your projects or need assistance, please contact our support team.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
