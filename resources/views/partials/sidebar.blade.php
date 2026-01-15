<div id="sidebar-wrapper" style="background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <!-- Logo/Brand -->
    <div class="sidebar-heading text-center py-4 border-bottom border-white border-opacity-10">
        <img src="{{ asset('assets/images/tracklyt-logo-drk-wide.png') }}" 
             alt="Tracklyt" 
             class="sidebar-logo mb-2"
             style="max-height: 50px; max-width: 180px; filter: brightness(0) invert(1);">
        @if(!auth()->user()->isSuperAdmin() && auth()->user()->tenant)
        <small class="text-white text-opacity-75 d-block mt-1">{{ auth()->user()->tenant->name }}</small>
        @endif
    </div>

    <!-- Navigation -->
    <nav class="py-3">
        <!-- Dashboard -->
        <a href="{{ route('home') }}" class="sidebar-nav-link {{ request()->routeIs('home') || request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-3"></i>Dashboard
        </a>

        @if(auth()->user()->isCentralUser())
        <!-- Super Admin Section -->
        <div class="sidebar-section-header">PLATFORM ADMIN</div>
        <a href="{{ route('admin.tenants.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
            <i class="bi bi-building me-3"></i>Tenants
        </a>
        @endif

        <!-- Clients & Projects -->
        <div class="sidebar-section-header">BUSINESS</div>
        <a href="{{ route('clients.index') }}" class="sidebar-nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
            <i class="bi bi-people me-3"></i>Clients
        </a>
        <a href="{{ route('projects.index') }}" class="sidebar-nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
            <i class="bi bi-kanban me-3"></i>Projects
        </a>

        <!-- Time Tracking -->
        <div class="sidebar-section-header">TIME TRACKING</div>
        <a href="{{ route('tasks.index') }}" class="sidebar-nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <i class="bi bi-check2-square me-3"></i>Tasks
        </a>
        <a href="{{ route('time-entries.index') }}" class="sidebar-nav-link {{ request()->routeIs('time-entries.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history me-3"></i>Time Entries
        </a>

        <!-- Maintenance -->
        <div class="sidebar-section-header">MAINTENANCE</div>
        <a href="{{ route('maintenance-profiles.index') }}" class="sidebar-nav-link {{ request()->routeIs('maintenance-profiles.*') ? 'active' : '' }}">
            <i class="bi bi-wrench me-3"></i>Retainers
        </a>
        <a href="{{ route('maintenance-reports.index') }}" class="sidebar-nav-link {{ request()->routeIs('maintenance-reports.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check me-3"></i>Reports
        </a>

        <!-- Billing -->
        <div class="sidebar-section-header">BILLING</div>
        
        <a href="{{ route('quotes.index') }}" class="sidebar-nav-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
            <i class="bi bi-file-text me-3"></i>Quotes
        </a>
        <a href="{{ route('invoices.index') }}" class="sidebar-nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="bi bi-receipt me-3"></i>Invoices
        </a>

        @if(auth()->user()->isAdmin())
        <!-- Admin Section -->
        <div class="sidebar-section-header">ADMIN</div>
        
        <a href="{{ route('reports.index') }}" class="sidebar-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart me-3"></i>Stats Reports
        </a>
        <a href="{{ route('settings.index') }}" class="sidebar-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear me-3"></i>Settings
        </a>
        <a href="{{ route('access-control.index') }}" class="sidebar-nav-link {{ request()->routeIs('access-control.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock me-3"></i>Access Control
        </a>
        @endif
    </nav>
</div>

<style>
.sidebar-nav-link {
    display: block;
    padding: 0.75rem 1.5rem;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.sidebar-nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border-left-color: rgba(255, 255, 255, 0.5);
}

.sidebar-nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border-left-color: #ffffff;
    font-weight: 500;
}

.sidebar-section-header {
    padding: 1rem 1.5rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
</style>
