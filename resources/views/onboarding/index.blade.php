<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Tracklyt - Setup Your Workspace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .onboarding-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .onboarding-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .progress-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            color: white;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255,255,255,0.3);
            z-index: 0;
        }
        .step {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .step.active .step-circle {
            background: white;
            color: #667eea;
            transform: scale(1.2);
        }
        .step.completed .step-circle {
            background: #10b981;
            color: white;
        }
        .step-label {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        .step.active .step-label {
            opacity: 1;
            font-weight: 600;
        }
        .onboarding-body {
            padding: 3rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="onboarding-container">
            <div class="onboarding-card">
                <!-- Progress Header -->
                <div class="progress-header">
                    <div class="text-center mb-3">
                        <h2 class="mb-2">
                            <i class="bi bi-clipboard-data me-2"></i>Welcome to Tracklyt
                        </h2>
                        <p class="mb-0 opacity-75">Let's set up your workspace in a few simple steps</p>
                    </div>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step {{ $step >= 1 ? 'active' : '' }} {{ $step > 1 ? 'completed' : '' }}">
                            <div class="step-circle">
                                @if($step > 1)
                                <i class="bi bi-check"></i>
                                @else
                                1
                                @endif
                            </div>
                            <div class="step-label">Company</div>
                        </div>
                        <div class="step {{ $step >= 2 ? 'active' : '' }} {{ $step > 2 ? 'completed' : '' }}">
                            <div class="step-circle">
                                @if($step > 2)
                                <i class="bi bi-check"></i>
                                @else
                                2
                                @endif
                            </div>
                            <div class="step-label">Team</div>
                        </div>
                        <div class="step {{ $step >= 3 ? 'active' : '' }} {{ $step > 3 ? 'completed' : '' }}">
                            <div class="step-circle">
                                @if($step > 3)
                                <i class="bi bi-check"></i>
                                @else
                                3
                                @endif
                            </div>
                            <div class="step-label">Quick Start</div>
                        </div>
                        <div class="step {{ $step >= 4 ? 'active' : '' }}">
                            <div class="step-circle">4</div>
                            <div class="step-label">Preferences</div>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="onboarding-body">
                    @if($step == 1)
                        @include('onboarding.steps.company')
                    @elseif($step == 2)
                        @include('onboarding.steps.team')
                    @elseif($step == 3)
                        @include('onboarding.steps.quick-start')
                    @elseif($step == 4)
                        @include('onboarding.steps.preferences')
                    @endif
                </div>
            </div>
            
            <!-- Skip Option -->
            <div class="text-center mt-3">
                <form action="{{ route('onboarding.skip') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-white" onclick="return confirm('Skip onboarding? You can complete setup later in Settings.')">
                        Skip for now <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
