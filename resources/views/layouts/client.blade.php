<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Client Portal - ' . config('app.name', 'Tracklyt'))</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tracklyt Theme -->
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/theme.css') }}" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}"/>

    <style>
        .client-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .client-nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s;
        }
        
        .client-nav-link:hover,
        .client-nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .stat-card {
            border-left: 4px solid #667eea;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Client Navigation -->
    <nav class="client-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('client.dashboard') }}" class="text-white text-decoration-none">
                        <h4 class="mb-0">
                            <i class="bi bi-building me-2"></i>
                            {{ config('app.name', 'Tracklyt') }}
                        </h4>
                    </a>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('client.dashboard') }}" 
                       class="client-nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                    <a href="{{ route('client.quotes.index') }}" 
                       class="client-nav-link {{ request()->routeIs('client.quotes.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-1"></i> Quotes
                    </a>
                    <a href="{{ route('client.projects.index') }}" 
                       class="client-nav-link {{ request()->routeIs('client.projects.*') ? 'active' : '' }}">
                        <i class="bi bi-briefcase me-1"></i> Projects
                    </a>
                    <a href="{{ route('client.invoices.index') }}" 
                       class="client-nav-link {{ request()->routeIs('client.invoices.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt me-1"></i> Invoices
                    </a>
                    <a href="{{ route('client.logout') }}" class="client-nav-link">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Alerts -->
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

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-5">
        <div class="container text-center text-muted">
            <small>&copy; {{ date('Y') }} {{ config('app.name', 'Tracklyt') }}. All rights reserved.</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
