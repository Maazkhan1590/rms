@extends('layouts.public')

@section('title', 'Login | Academic Research Portal')

@section('content')
<!-- Login Section -->
<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your Research Portal account</p>
            </div>
            <form method="POST" action="{{ route('login') }}" id="login-form" class="auth-form">
                @csrf

                @if ($errors->any())
                    <div style="background: #fee; color: #c33; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #c33;">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 0.5rem 0 0 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div style="background: #efe; color: #3c3; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #3c3;">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" class="form-control" placeholder="Enter your email address" value="{{ old('email') }}" required autofocus>
                    <div class="form-error" id="email-error"></div>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div class="password-input">
                        <input type="password" id="login-password" name="password" class="form-control" placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" id="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-error" id="password-error"></div>
                </div>
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>
            <div class="auth-footer">
                <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
            </div>
        </div>
    </div>
</section>
@endsection
