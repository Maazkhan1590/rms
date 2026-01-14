@extends('layouts.auth')

@section('title', 'Password Reset Successful - RMS')

@section('content')
<div class="auth-container">
    <div class="auth-card text-center">
        <!-- Success Icon -->
        <div class="mb-4">
            <div class="success-icon mb-3">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="#28a745" fill-opacity="0.1"/>
                    <circle cx="40" cy="40" r="32" fill="#28a745"/>
                    <path d="M25 40L35 50L55 30" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h4 class="fw-bold text-success mb-2">Password Reset Successful!</h4>
            <p class="text-muted">Your password has been changed successfully.</p>
        </div>

        <!-- Success Message -->
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            You can now log in with your new password.
        </div>

        <!-- Action Buttons -->
        <a href="{{ route('login') }}" class="btn btn-primary w-100 py-2 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
        </a>

        <!-- Additional Info -->
        <div class="mt-4">
            <p class="text-muted small mb-2">
                <i class="bi bi-shield-check me-1"></i>
                For security reasons, you have been logged out of all devices.
            </p>
            <p class="text-muted small mb-0">
                If you didn't request this password reset, please 
                <a href="mailto:support@rms.uos.edu.pk">contact support</a> immediately.
            </p>
        </div>
    </div>
</div>

@push('styles')
<style>
.success-icon {
    animation: scaleIn 0.5s ease-in-out;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Auto redirect to login after 5 seconds
setTimeout(() => {
    window.location.href = "{{ route('login') }}";
}, 5000);

// Countdown timer
let countdown = 5;
const countdownInterval = setInterval(() => {
    countdown--;
    if (countdown <= 0) {
        clearInterval(countdownInterval);
    }
}, 1000);
</script>
@endpush
@endsection
