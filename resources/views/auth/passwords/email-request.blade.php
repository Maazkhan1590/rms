@extends('layouts.auth')

@section('title', 'Forgot Password - RMS')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header text-center mb-4">
            <div class="su-logo mb-3">
                <img src="{{ asset('images/su-logo.png') }}" alt="RMS Logo" class="img-fluid" style="max-width: 60px;"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22%230056b3%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2230%22 font-weight=%22bold%22>SU</text></svg>';">
            </div>
            <h4 class="fw-bold text-primary mb-2">Forgot Password?</h4>
            <p class="text-muted small">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
            @csrf

            <!-- Success Message -->
            @if (session('success') || session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') ?? session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Email Field -->
            <div class="form-floating mb-4">
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       placeholder="name@example.com" 
                       value="{{ old('email') }}"
                       required 
                       autofocus>
                <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3" id="submitBtn">
                <i class="bi bi-send me-2"></i>Send Reset Link
            </button>

            <!-- Back to Login -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left me-2"></i>Back to Login
                </a>
            </div>
        </form>

        <!-- Info Box -->
        <div class="alert alert-info mt-4 small" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> The password reset link will expire in 60 minutes. If you don't receive an email, check your spam folder or contact support.
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
});

// Auto-hide alerts after 8 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 8000);
</script>
@endpush
@endsection
