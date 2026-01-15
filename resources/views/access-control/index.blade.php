@extends('layouts.app')

{{-- @section('header', 'Access Control') --}}

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Access Control</h2>
            <p class="text-muted mb-0">Manage roles, permissions, and user access</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
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

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('access-control.index', ['section' => 'roles']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'roles' ? 'active' : '' }}">
                        <i class="bi bi-shield-check me-2"></i>Roles
                    </a>
                    <a href="{{ route('access-control.index', ['section' => 'permissions']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'permissions' ? 'active' : '' }}">
                        <i class="bi bi-key me-2"></i>Permissions
                    </a>
                    <a href="{{ route('access-control.index', ['section' => 'users']) }}" 
                       class="list-group-item list-group-item-action {{ $section === 'users' ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i>User Access
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-md-9">
            @if($section === 'roles')
                @include('access-control.sections.roles')
            @elseif($section === 'permissions')
                @include('access-control.sections.permissions')
            @elseif($section === 'users')
                @include('access-control.sections.users')
            @endif
        </div>
    </div>
</div>
@endsection
