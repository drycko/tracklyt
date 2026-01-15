<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Tracklyt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .complete-container {
            max-width: 600px;
            width: 100%;
            padding: 2rem;
        }
        .complete-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon i {
            font-size: 3rem;
            color: white;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        .complete-card h1 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .complete-card p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .feature-list {
            text-align: left;
            margin: 2rem 0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .feature-item i {
            color: #667eea;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="complete-container">
        <div class="complete-card">
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            
            <h1>You're All Set!</h1>
            <p>Your workspace has been successfully configured and is ready to use.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <i class="bi bi-people-fill"></i>
                    <div>
                        <strong>Team Management</strong>
                        <small class="d-block text-muted">Collaborate with your team members</small>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="bi bi-folder-fill"></i>
                    <div>
                        <strong>Project Tracking</strong>
                        <small class="d-block text-muted">Manage projects and tasks efficiently</small>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="bi bi-clock-fill"></i>
                    <div>
                        <strong>Time Tracking</strong>
                        <small class="d-block text-muted">Track billable hours accurately</small>
                    </div>
                </div>
                <div class="feature-item">
                    <i class="bi bi-file-text-fill"></i>
                    <div>
                        <strong>Invoicing & Quotes</strong>
                        <small class="d-block text-muted">Create professional invoices</small>
                    </div>
                </div>
            </div>

            <a href="{{ route('home') }}" class="btn btn-primary">
                Go to Dashboard <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
