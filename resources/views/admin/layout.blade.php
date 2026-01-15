<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tracklyt Theme -->
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/utilities.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        #wrapper {
            overflow-x: hidden;
        }
        
        #sidebar-wrapper {
            min-width: 260px;
            max-width: 260px;
            transition: margin 0.25s ease;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        #page-content-wrapper {
            width: 100%;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .admin-nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
            background-color: transparent !important;
        }
        
        .admin-nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            transform: translateX(5px);
        }
        
        .admin-nav-link.active {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            font-weight: 600;
            transform: translateX(5px);
            box-shadow: inset 4px 0 0 #ffffff;
        }
        
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -260px;
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }
        }
        
        .admin-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Admin Sidebar -->
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check fs-4 text-white me-2"></i>
                    <h5 class="mb-0 text-white fw-bold">{{ config('app.name') }} Admin</h5>
                </div>
            </div>
            <div class="list-group list-group-flush p-3">
                <a href="{{ route('admin.dashboard') }}" 
                   class="list-group-item list-group-item-action admin-nav-link border-0 {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                
                <a href="{{ route('admin.tenants.index') }}" 
                   class="list-group-item list-group-item-action admin-nav-link border-0 {{ request()->routeIs('admin.tenants*') ? 'active' : '' }}">
                    <i class="bi bi-buildings me-2"></i>Tenants
                </a>

                <a href="{{ route('admin.subscriptions.index') }}" 
                   class="list-group-item list-group-item-action admin-nav-link border-0 {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front me-2"></i>Subscriptions
                </a>

                <a href="{{ route('admin.plans.index') }}" 
                   class="list-group-item list-group-item-action admin-nav-link border-0 {{ request()->routeIs('admin.plans*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3 me-2"></i>Plans
                </a>

                <a href="{{ route('admin.billing.index') }}" 
                   class="list-group-item list-group-item-action admin-nav-link border-0 {{ request()->routeIs('admin.billing*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow me-2"></i>Billing
                </a>

                <hr class="my-3" style="border-color: rgba(255, 255, 255, 0.2);">
                
                <a href="{{ route('home') }}" 
                   class="list-group-item list-group-item-action admin-nav-link border-0">
                    <i class="bi bi-arrow-left me-2"></i>Back to App
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-outline-secondary" id="sidebarToggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Platform Admin</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="container-fluid p-4" style="min-height: calc(100vh - 70px);">
                <!-- Flash Messages -->
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

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Header -->
                @hasSection('header')
                <div class="mb-4">
                    @hasSection('breadcrumb')
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb breadcrumb-sm mb-0" style="font-size: 0.875rem; background: transparent; padding: 0;">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">@yield('header')</h1>
                        @hasSection('actions')
                        <div>
                            @yield('actions')
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const wrapper = document.getElementById('wrapper');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    wrapper.classList.toggle('toggled');
                });
            }
            
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>