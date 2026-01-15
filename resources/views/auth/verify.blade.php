@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="bi bi-envelope-check"></i>
        </div>
        <h2 class="mb-0">Verify Email Address</h2>
        <p class="mb-0 opacity-75">Check your inbox for verification link</p>
    </div>
    <div class="auth-body">
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle me-2"></i>A fresh verification link has been sent to your email address.
            </div>
        @endif

        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>Before proceeding, please check your email for a verification link.
        </div>

        <p class="text-center text-muted">If you did not receive the email:</p>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Resend Verification Email
                </button>
            </div>
        </form>

        <hr class="my-4">
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-muted">
                <i class="bi bi-arrow-left me-2"></i>Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
