@extends('layouts.auth')

@section('title', 'Email Verified - RMS')

@section('content')
<div class="auth-container">
    <div class="auth-card text-center">
        <!-- Success Icon with Animation -->
        <div class="mb-4">
            <div class="verified-icon mb-3">
                <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="50" fill="#28a745" fill-opacity="0.1" class="outer-circle"/>
                    <circle cx="50" cy="50" r="40" fill="#28a745" class="inner-circle"/>
                    <path d="M30 50L43 63L70 36" stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="checkmark"/>
                </svg>
            </div>
            <h4 class="fw-bold text-success mb-2">Email Verified Successfully!</h4>
            <p class="text-muted">Your email address has been confirmed.</p>
        </div>

        <!-- Status Message -->
        <div class="alert alert-info mb-4" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Account Pending Approval</strong><br>
            <small>Your account is currently under review by our administrators. You'll receive a notification once approved.</small>
        </div>

        <!-- Next Steps -->
        <div class="card mb-4">
            <div class="card-body text-start">
                <h6 class="card-title fw-semibold mb-3">
                    <i class="bi bi-list-check me-2 text-primary"></i>What Happens Next?
                </h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-1-circle-fill text-primary me-2"></i>
                        Our team reviews your credentials
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-2-circle-fill text-primary me-2"></i>
                        You'll receive an email notification
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-3-circle-fill text-primary me-2"></i>
                        Access your dashboard once approved
                    </li>
                </ul>
            </div>
        </div>

        <!-- Estimated Time -->
        <p class="text-muted small mb-4">
            <i class="bi bi-clock me-1"></i>
            Approval typically takes 1-2 business days
        </p>

        <!-- Action Buttons -->
        <div class="d-grid gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-primary py-2">
                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100 py-2">
                    <i class="bi bi-box-arrow-left me-2"></i>Logout
                </button>
            </form>
        </div>

        <!-- Support Info -->
        <div class="mt-4">
            <p class="text-muted small mb-0">
                Questions? <a href="mailto:support@rms.uos.edu.pk" class="text-decoration-none">Contact Support</a>
            </p>
        </div>
    </div>
</div>

@push('styles')
<style>
.verified-icon {
    animation: scaleIn 0.6s ease-out;
}

@keyframes scaleIn {
    0% {
        transform: scale(0) rotate(0deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.1) rotate(180deg);
    }
    100% {
        transform: scale(1) rotate(360deg);
        opacity: 1;
    }
}

.outer-circle {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.3;
    }
    50% {
        opacity: 0.6;
    }
}

.checkmark {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: drawCheck 0.5s ease-out 0.3s forwards;
}

@keyframes drawCheck {
    to {
        stroke-dashoffset: 0;
    }
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
}
</style>
@endpush
@endsection
