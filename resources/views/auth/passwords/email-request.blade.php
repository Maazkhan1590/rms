@extends('layouts.public')

@section('title', 'Forgot Password | Academic Research Portal')

@section('content')
<!-- Forgot Password Section -->
<section class="auth-section">
    <div class="container">
        <div class="auth-container" style="max-width: 500px;">
            <div class="auth-header">
                <h1>Forgot Password?</h1>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>
            <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm" class="auth-form">
                @csrf

                @if (session('success') || session('status'))
                    <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #28a745; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
                        <div>
                            <strong>Success!</strong>
                            <p style="margin: 0.25rem 0 0 0;">{{ session('success') ?? session('status') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div style="background: #fee; color: #c33; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #c33;">
                        <strong style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Please fix the following errors:
                        </strong>
                        <ul style="margin: 0.5rem 0 0 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email">Email Address <span style="color: #ef4444;">*</span></label>
                    <div style="position: relative;">
                        <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-lighter); z-index: 1;"></i>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Enter your email address" 
                               value="{{ old('email') }}"
                               required 
                               autofocus
                               style="padding-left: 2.75rem;">
                    </div>
                    <div class="form-error" id="email-error"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            
            <div class="auth-footer">
                <p>
                    <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </p>
            </div>

            <!-- Info Box -->
            <div style="background: linear-gradient(135deg, #e3f2fd 0%, #f0f4ff 100%); padding: 1.25rem; border-radius: 12px; margin-top: 1.5rem; border-left: 4px solid var(--accent-color);">
                <div style="display: flex; align-items: start; gap: 0.75rem;">
                    <i class="fas fa-info-circle" style="color: var(--accent-color); font-size: 1.25rem; margin-top: 0.125rem;"></i>
                    <div>
                        <strong style="color: var(--primary-color); display: block; margin-bottom: 0.5rem;">Note:</strong>
                        <p style="color: var(--text-light); margin: 0; font-size: 0.9rem; line-height: 1.6;">
                            The password reset link will expire in 60 minutes. If you don't receive an email, please check your spam folder or contact support.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
});

// Auto-hide success messages after 8 seconds
setTimeout(() => {
    const successMessages = document.querySelectorAll('[style*="background: #d4edda"]');
    successMessages.forEach(msg => {
        msg.style.opacity = '0';
        msg.style.transition = 'opacity 0.5s';
        setTimeout(() => msg.remove(), 500);
    });
}, 8000);
</script>
@endpush
@endsection
