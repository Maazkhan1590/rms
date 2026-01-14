@extends('layouts.auth')

@section('title', 'Reset Password - RMS')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header text-center mb-4">
            <div class="su-logo mb-3">
                <img src="{{ asset('images/su-logo.png') }}" alt="RMS Logo" class="img-fluid" style="max-width: 60px;"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22%230056b3%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2230%22 font-weight=%22bold%22>SU</text></svg>';">
            </div>
            <h4 class="fw-bold text-primary mb-2">Reset Your Password</h4>
            <p class="text-muted small">Create a new secure password for your account</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

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
            <div class="form-floating mb-3">
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       placeholder="name@example.com" 
                       value="{{ $email ?? old('email') }}"
                       required 
                       readonly>
                <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password Field -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">
                    <i class="bi bi-lock me-2"></i>New Password
                </label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password"
                       required>
                
                <!-- Password Strength Meter -->
                <div class="password-strength mt-2">
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar" id="passwordStrength" role="progressbar"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted" id="strengthText">Password strength</small>
                        <button type="button" class="btn btn-sm btn-link p-0 text-muted" onclick="togglePassword('password')">
                            <i class="bi bi-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                </div>

                <!-- Password Requirements -->
                <ul class="list-unstyled small text-muted mt-2 mb-0">
                    <li id="req-length"><i class="bi bi-circle"></i> At least 8 characters</li>
                    <li id="req-uppercase"><i class="bi bi-circle"></i> One uppercase letter</li>
                    <li id="req-lowercase"><i class="bi bi-circle"></i> One lowercase letter</li>
                    <li id="req-number"><i class="bi bi-circle"></i> One number</li>
                    <li id="req-special"><i class="bi bi-circle"></i> One special character</li>
                </ul>

                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold">
                    <i class="bi bi-lock-fill me-2"></i>Confirm New Password
                </label>
                <div class="position-relative">
                    <input type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           required>
                    <button type="button" class="btn btn-sm btn-link password-toggle" onclick="togglePassword('password_confirmation')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                        <i class="bi bi-eye" id="toggleIcon2"></i>
                    </button>
                </div>
                <div id="passwordMatch" class="small mt-1"></div>
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3" id="submitBtn">
                <i class="bi bi-check-circle me-2"></i>Reset Password
            </button>

            <!-- Back to Login -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left me-2"></i>Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId === 'password' ? 'toggleIcon1' : 'toggleIcon2');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Password strength meter and requirements checker
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    let strength = 0;
    
    // Check requirements
    const requirements = {
        'req-length': password.length >= 8,
        'req-uppercase': /[A-Z]/.test(password),
        'req-lowercase': /[a-z]/.test(password),
        'req-number': /[0-9]/.test(password),
        'req-special': /[$@#&!]/.test(password)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(req => {
        const element = document.getElementById(req);
        if (requirements[req]) {
            element.innerHTML = element.innerHTML.replace('bi-circle', 'bi-check-circle-fill text-success');
            strength += 20;
        } else {
            element.innerHTML = element.innerHTML.replace('bi-check-circle-fill text-success', 'bi-circle');
        }
    });
    
    // Update strength bar
    const bar = document.getElementById('passwordStrength');
    const text = document.getElementById('strengthText');
    
    bar.style.width = strength + '%';
    
    if (strength < 40) {
        bar.className = 'progress-bar bg-danger';
        text.textContent = 'Weak password';
    } else if (strength < 80) {
        bar.className = 'progress-bar bg-warning';
        text.textContent = 'Fair password';
    } else {
        bar.className = 'progress-bar bg-success';
        text.textContent = 'Strong password';
    }
});

// Password match checker
document.getElementById('password_confirmation').addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const confirm = e.target.value;
    const matchDiv = document.getElementById('passwordMatch');
    
    if (confirm.length > 0) {
        if (password === confirm) {
            matchDiv.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i>Passwords match';
            matchDiv.className = 'small mt-1 text-success';
        } else {
            matchDiv.innerHTML = '<i class="bi bi-x-circle-fill text-danger me-1"></i>Passwords do not match';
            matchDiv.className = 'small mt-1 text-danger';
        }
    } else {
        matchDiv.innerHTML = '';
    }
});

// Form submission
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Resetting Password...';
});
</script>
@endpush
@endsection
