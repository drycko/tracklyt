<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Tracklyt'))</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tracklyt Theme -->
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/utilities.css') }}" rel="stylesheet">

    {{-- icon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}"/>
    {{-- bootstrap icons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/images/site.webmanifest') }}">
    
    <style>
        #wrapper {
            overflow-x: hidden;
        }
        
        #sidebar-wrapper {
            min-width: 260px;
            max-width: 260px;
            transition: margin 0.25s ease;
        }
        
        #page-content-wrapper {
            width: 100%;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -260px;
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top Navbar -->
            @include('partials.navbar')

            <!-- Page Content -->
            <main class="container-fluid p-4" style="min-height: calc(100vh - 70px);">
                <!-- Alerts -->
                @include('partials.alerts')
                
                <!-- Page Header with Integrated Breadcrumb -->
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
            
            // Initialize Bootstrap tooltips if needed
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
