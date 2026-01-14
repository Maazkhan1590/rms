@extends('layouts.auth')

@section('title', 'Verify Your Email - RMS')

@section('content')
<div class="auth-container">
    <div class="auth-card text-center">
        <!-- Email Icon -->
        <div class="mb-4">
            <div class="email-icon mb-3">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="#0056b3" fill-opacity="0.1"/>
                    <circle cx="40" cy="40" r="32" fill="#0056b3"/>
                    <path d="M25 30L40 42L55 30M25 30V50H55V30H25Z" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h4 class="fw-bold text-primary mb-2">Verify Your Email Address</h4>
            <p class="text-muted">Before proceeding, please check your email for a verification link.</p>
        </div>

        @if (session('resent'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                A fresh verification link has been sent to your email address.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Instructions -->
        <div class="alert alert-info mb-4" role="alert">
            <p class="mb-2">
                <i class="bi bi-envelope me-2"></i>
                We've sent a verification email to:
            </p>
            <strong>{{ Auth::user()->email ?? 'your email address' }}</strong>
        </div>

        <!-- Instructions Steps -->
        <div class="text-start mb-4">
            <h6 class="fw-semibold mb-3">Next Steps:</h6>
            <ol class="ps-3">
                <li class="mb-2">Check your inbox for the verification email</li>
                <li class="mb-2">Click the verification link in the email</li>
                <li class="mb-2">You'll be redirected back here automatically</li>
            </ol>
        </div>

        <!-- Resend Form -->
        <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
            @csrf
            <button type="submit" class="btn btn-outline-primary w-100 py-2 mb-3" id="resendBtn">
                <i class="bi bi-arrow-clockwise me-2"></i>Resend Verification Email
            </button>
        </form>

        <div class="divider my-3">
            <span class="divider-text">OR</span>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none">
                <i class="bi bi-box-arrow-left me-2"></i>Logout
            </button>
        </form>

        <!-- Additional Help -->
        <div class="mt-4">
            <p class="text-muted small mb-2">
                <i class="bi bi-info-circle me-1"></i>
                Didn't receive the email?
            </p>
            <ul class="list-unstyled small text-muted">
                <li>Check your spam/junk folder</li>
                <li>Make sure the email address is correct</li>
                <li>Wait a few minutes and try resending</li>
            </ul>
            <p class="text-muted small mt-3">
                Need help? <a href="mailto:support@rms.uos.edu.pk">Contact Support</a>
            </p>
        </div>
    </div>
</div>

@push('styles')
<style>
.email-icon {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

ol li {
    color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
// Resend button with cooldown
let cooldownTime = 0;
const resendBtn = document.getElementById('resendBtn');
const resendForm = document.getElementById('resendForm');

// Check if there's a cooldown in localStorage
const lastResend = localStorage.getItem('lastResend');
if (lastResend) {
    const timePassed = Date.now() - parseInt(lastResend);
    const cooldownDuration = 60000; // 60 seconds
    
    if (timePassed < cooldownDuration) {
        cooldownTime = Math.ceil((cooldownDuration - timePassed) / 1000);
        startCooldown();
    }
}

resendForm.addEventListener('submit', function(e) {
    if (cooldownTime > 0) {
        e.preventDefault();
        return false;
    }
    
    localStorage.setItem('lastResend', Date.now().toString());
    resendBtn.disabled = true;
    resendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
});

function startCooldown() {
    resendBtn.disabled = true;
    const interval = setInterval(() => {
        cooldownTime--;
        resendBtn.innerHTML = `<i class="bi bi-clock me-2"></i>Resend in ${cooldownTime}s`;
        
        if (cooldownTime <= 0) {
            clearInterval(interval);
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Resend Verification Email';
        }
    }, 1000);
}

// Auto-hide success alert after 5 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush
@endsection
