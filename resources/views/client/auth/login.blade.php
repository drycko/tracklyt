<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Client Login - {{ config('app.name', 'Tracklyt') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}"/>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
        }
        
        .method-btn {
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .method-btn:hover,
        .method-btn.active {
            border-color: #667eea;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card mx-auto p-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Client Portal</h2>
                <p class="text-muted">Access your project information</p>
            </div>

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

            <form action="{{ route('client.send-magic-link') }}" method="POST">
                @csrf

                <!-- Method Selection -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Select Login Method</label>
                    <div class="row g-3">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="method" id="method-email" value="email" checked>
                            <label class="btn method-btn w-100 py-3" for="method-email">
                                <i class="bi bi-envelope d-block fs-3 mb-2"></i>
                                Email
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="method" id="method-whatsapp" value="whatsapp">
                            <label class="btn method-btn w-100 py-3" for="method-whatsapp">
                                <i class="bi bi-whatsapp d-block fs-3 mb-2"></i>
                                WhatsApp
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Email Input -->
                <div id="email-input" class="mb-4">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control form-control-lg @error('identifier') is-invalid @enderror" 
                           id="email" name="identifier" placeholder="your@email.com" required>
                    @error('identifier')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- WhatsApp Input (hidden by default) -->
                <div id="whatsapp-input" class="mb-4" style="display: none;">
                    <label for="whatsapp" class="form-label fw-semibold">WhatsApp Number</label>
                    <input type="tel" class="form-control form-control-lg" 
                           id="whatsapp" placeholder="+1234567890">
                    <small class="text-muted">Include country code (e.g., +1234567890)</small>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-send me-2"></i>Send Magic Link
                </button>

                <p class="text-center text-muted mt-4 mb-0">
                    <small>A secure link will be sent to authenticate you</small>
                </p>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle between email and WhatsApp inputs
        const emailRadio = document.getElementById('method-email');
        const whatsappRadio = document.getElementById('method-whatsapp');
        const emailInput = document.getElementById('email-input');
        const whatsappInput = document.getElementById('whatsapp-input');
        const emailField = document.getElementById('email');
        const whatsappField = document.getElementById('whatsapp');

        emailRadio.addEventListener('change', function() {
            if (this.checked) {
                emailInput.style.display = 'block';
                whatsappInput.style.display = 'none';
                emailField.name = 'identifier';
                emailField.required = true;
                whatsappField.name = '';
                whatsappField.required = false;
            }
        });

        whatsappRadio.addEventListener('change', function() {
            if (this.checked) {
                emailInput.style.display = 'none';
                whatsappInput.style.display = 'block';
                whatsappField.name = 'identifier';
                whatsappField.required = true;
                emailField.name = '';
                emailField.required = false;
            }
        });
    </script>
</body>
</html>
