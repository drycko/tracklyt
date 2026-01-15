<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Tracklyt') }} - Time Tracking & Project Management for Agencies</title>
    <meta name="description" content="Streamline your agency workflow from quote to invoice. Track time, manage projects, and bill clients with Tracklyt.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tracklyt Theme -->
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/theme.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/tracklyt-bs5-theme/css/utilities.css') }}" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
        }
        .feature-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            height: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        .feature-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background: white;
            color: #667eea;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 0.5rem;
        }
        .btn-primary:hover {
            background: #f8f9fa;
            color: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .btn-outline-light {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border-width: 2px;
        }
        .workflow-step {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
            text-align: center;
        }
        .workflow-arrow {
            font-size: 2rem;
            color: white;
        }
        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 3rem;
            color: white;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark position-absolute w-100" style="z-index: 1000;">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="{{ url('/') }}">
                <i class="bi bi-clock-history me-2"></i>Tracklyt
            </a>
            @if (Route::has('login'))
                <div class="ms-auto">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-light">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-light">Get Started</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="display-3 fw-bold mb-4">Complete Agency Workflow Management</h1>
                    <p class="lead mb-4">From quote to invoice, Tracklyt streamlines your entire project lifecycle. Track time, manage deliverables, handle maintenance, and get paid faster.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Start Free Trial</a>
                        @endif
                        <a href="#features" class="btn btn-outline-light">Learn More</a>
                    </div>
                    <div class="mt-4">
                        <small class="opacity-75">✓ No credit card required &nbsp;|&nbsp; ✓ 14-day free trial &nbsp;|&nbsp; ✓ Cancel anytime</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="stats-section">
                        <div class="row text-center">
                            <div class="col-6 mb-4">
                                <div class="stat-number">100%</div>
                                <div>Visibility</div>
                            </div>
                            <div class="col-6 mb-4">
                                <div class="stat-number">50%</div>
                                <div>Time Saved</div>
                            </div>
                            <div class="col-6">
                                <div class="stat-number">2x</div>
                                <div>Faster Billing</div>
                            </div>
                            <div class="col-6">
                                <div class="stat-number">∞</div>
                                <div>Peace of Mind</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="py-5" style="background: rgba(255, 255, 255, 0.05);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-white mb-3">Your Complete Workflow</h2>
                <p class="lead text-white opacity-75">Everything you need in one place</p>
            </div>
            <div class="row g-4 align-items-center">
                <div class="col-md-2">
                    <div class="workflow-step">
                        <i class="bi bi-file-text fs-1 mb-3"></i>
                        <h5>Quote</h5>
                    </div>
                </div>
                <div class="col-md-1 text-center">
                    <i class="bi bi-arrow-right workflow-arrow"></i>
                </div>
                <div class="col-md-2">
                    <div class="workflow-step">
                        <i class="bi bi-kanban fs-1 mb-3"></i>
                        <h5>Project</h5>
                    </div>
                </div>
                <div class="col-md-1 text-center">
                    <i class="bi bi-arrow-right workflow-arrow"></i>
                </div>
                <div class="col-md-2">
                    <div class="workflow-step">
                        <i class="bi bi-clock-history fs-1 mb-3"></i>
                        <h5>Track Time</h5>
                    </div>
                </div>
                <div class="col-md-1 text-center">
                    <i class="bi bi-arrow-right workflow-arrow"></i>
                </div>
                <div class="col-md-2">
                    <div class="workflow-step">
                        <i class="bi bi-receipt fs-1 mb-3"></i>
                        <h5>Invoice</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5" style="background: white;">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Built for Agencies & Developers</h2>
                <p class="lead text-muted">Everything you need to run your business efficiently</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="mb-3">Client Management</h4>
                        <p class="text-muted">Keep all client information, contacts, and project history in one organized place.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h4 class="mb-3">Smart Quoting</h4>
                        <p class="text-muted">Create professional quotes with line items, tech stack details, and automatic numbering.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h4 class="mb-3">Project Tracking</h4>
                        <p class="text-muted">Track repositories, demo links, staging environments, and mobile app deployments.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-stopwatch"></i>
                        </div>
                        <h4 class="mb-3">Time Tracking</h4>
                        <p class="text-muted">Built-in timer with automatic duration calculation and locking when invoiced.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h4 class="mb-3">Maintenance & Retainers</h4>
                        <p class="text-muted">Manage monthly retainer hours with automatic rollover and usage tracking.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <h4 class="mb-3">Automated Billing</h4>
                        <p class="text-muted">Generate invoices from time entries, automatically lock billed time, and get paid faster.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="display-4 fw-bold text-white mb-4">Ready to streamline your workflow?</h2>
                    <p class="lead text-white opacity-75 mb-4">Join agencies and developers who are saving time and getting paid faster.</p>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start Your Free Trial</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4" style="background: rgba(0, 0, 0, 0.3);">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-white">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-clock-history me-2"></i>Tracklyt
                    </h5>
                    <p class="opacity-75">Complete workflow management for agencies and developers.</p>
                </div>
                <div class="col-md-6 text-end text-white">
                    <p class="opacity-75 mb-0">&copy; {{ date('Y') }} Tracklyt. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Tracklyt Theme JS -->
    <script src="{{ asset('vendor/tracklyt-bs5-theme/js/theme.js') }}"></script>
</body>
</html>
