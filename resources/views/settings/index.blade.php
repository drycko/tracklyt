@extends('layouts.app')

@section('header', 'Settings')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Settings</h2>
            <p class="text-muted mb-0">Manage your account and workspace settings</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    {{-- @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif --}}

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('settings.index', ['section' => 'profile']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'profile' ? 'active' : '' }}">
                        <i class="bi bi-person me-2"></i>My Profile
                    </a>
                    @if($user->isOwner() || $user->isAdmin() || $user->isCentralUser())
                    <a href="{{ route('settings.index', ['section' => 'company']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'company' ? 'active' : '' }}">
                        <i class="bi bi-building me-2"></i>Company Profile
                    </a>
                    <a href="{{ route('settings.index', ['section' => 'billing']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'billing' ? 'active' : '' }}">
                        <i class="bi bi-credit-card me-2"></i>Billing
                    </a>
                    <a href="{{ route('settings.index', ['section' => 'team']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'team' ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i>Team
                    </a>
                    <a href="{{ route('settings.index', ['section' => 'preferences']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'preferences' ? 'active' : '' }}">
                        <i class="bi bi-sliders me-2"></i>Preferences
                    </a>
                    <a href="{{ route('settings.index', ['section' => 'subscription']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'subscription' ? 'active' : '' }}">
                        <i class="bi bi-box me-2"></i>Subscription & Plan
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-md-9">
            @if($section === 'profile')
                @include('settings.sections.profile')
            @elseif($section === 'company')
                @include('settings.sections.company')
            @elseif($section === 'billing')
                @include('settings.sections.billing')
            @elseif($section === 'team')
                @include('settings.sections.team')
            @elseif($section === 'preferences')
                @include('settings.sections.preferences')
            @elseif($section === 'subscription')
                @include('settings.sections.subscription')
            @endif
        </div>
    </div>
</div>
@endsection
