@extends('layouts.guest')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="bi bi-clock-history"></i>
        </div>
        <h2 class="mb-0">Welcome Back</h2>
        <p class="mb-0 opacity-75">Log in to your Tracklyt account</p>
    </div>
    <div class="auth-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Remember Me
                </label>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                </button>
            </div>

            <div class="text-center">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-muted">
                        Forgot Your Password?
                    </a>
                @endif
            </div>

            @if (Route::has('register'))
                <hr class="my-4">
                <div class="text-center">
                    <p class="mb-0">Don't have an account? <a href="{{ route('register') }}" class="fw-bold">Sign Up</a></p>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
