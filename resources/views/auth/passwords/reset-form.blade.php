@extends('layouts.public')

@section('title', 'Reset Password | Academic Research Portal')

@section('content')
<!-- Reset Password Section -->
<section class="auth-section">
    <div class="container">
        <div class="auth-container" style="max-width: 550px;">
            <div class="auth-header">
                <h1>Reset Your Password</h1>
                <p>Create a new secure password for your account</p>
            </div>
            <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

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

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div style="position: relative;">
                        <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-lighter); z-index: 1;"></i>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Enter your email address" 
                               value="{{ $email ?? old('email') }}"
                               required 
                               readonly
                               style="padding-left: 2.75rem; background: var(--light-color);">
                    </div>
                    <div class="form-error" id="email-error"></div>
                </div>

                <!-- New Password Field -->
                <div class="form-group">
                    <label for="password">New Password <span style="color: #ef4444;">*</span></label>
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-lighter); z-index: 1;"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Enter your new password" 
                               required
                               style="padding-left: 2.75rem; padding-right: 3rem;">
                        <button type="button" class="toggle-password" id="toggle-password" onclick="togglePasswordVisibility('password', 'toggle-password-icon')" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-lighter); cursor: pointer; z-index: 1;">
                            <i class="fas fa-eye" id="toggle-password-icon"></i>
                        </button>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div id="password-strength" style="margin-top: 0.5rem; display: none;">
                        <div style="height: 4px; background: var(--border-light); border-radius: 2px; overflow: hidden; margin-bottom: 0.5rem;">
                            <div id="strength-bar" style="height: 100%; width: 0%; transition: all 0.3s; border-radius: 2px;"></div>
                        </div>
                        <small id="strength-text" style="color: var(--text-lighter); font-size: 0.85rem;"></small>
                    </div>

                    <!-- Password Requirements -->
                    <div id="password-requirements" style="margin-top: 0.75rem; padding: 0.75rem; background: var(--light-color); border-radius: 8px; display: none;">
                        <small style="color: var(--text-light); font-weight: 600; display: block; margin-bottom: 0.5rem;">Password must contain:</small>
                        <ul style="margin: 0; padding-left: 1.25rem; list-style: none;">
                            <li id="req-length" style="color: var(--text-lighter); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i> At least 8 characters
                            </li>
                            <li id="req-uppercase" style="color: var(--text-lighter); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i> One uppercase letter
                            </li>
                            <li id="req-lowercase" style="color: var(--text-lighter); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i> One lowercase letter
                            </li>
                            <li id="req-number" style="color: var(--text-lighter); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i> One number
                            </li>
                            <li id="req-special" style="color: var(--text-lighter); font-size: 0.85rem;">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.5rem;"></i> One special character
                            </li>
                        </ul>
                    </div>
                    <div class="form-error" id="password-error"></div>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password <span style="color: #ef4444;">*</span></label>
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-lighter); z-index: 1;"></i>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-control" 
                               placeholder="Confirm your new password" 
                               required
                               style="padding-left: 2.75rem; padding-right: 3rem;">
                        <button type="button" class="toggle-password" id="toggle-password-confirm" onclick="togglePasswordVisibility('password_confirmation', 'toggle-password-confirm-icon')" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-lighter); cursor: pointer; z-index: 1;">
                            <i class="fas fa-eye" id="toggle-password-confirm-icon"></i>
                        </button>
                    </div>
                    <div id="password-match" style="margin-top: 0.5rem; font-size: 0.85rem;"></div>
                    <div class="form-error" id="password-confirmation-error"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                    <i class="fas fa-check-circle"></i> Reset Password
                </button>
            </form>
            
            <div class="auth-footer">
                <p>
                    <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
// Toggle password visibility
function togglePasswordVisibility(fieldId, iconId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength checker
const passwordInput = document.getElementById('password');
const passwordStrength = document.getElementById('password-strength');
const strengthBar = document.getElementById('strength-bar');
const strengthText = document.getElementById('strength-text');
const passwordRequirements = document.getElementById('password-requirements');

passwordInput.addEventListener('input', function(e) {
    const password = e.target.value;
    
    if (password.length > 0) {
        passwordStrength.style.display = 'block';
        passwordRequirements.style.display = 'block';
    } else {
        passwordStrength.style.display = 'none';
        passwordRequirements.style.display = 'none';
        return;
    }
    
    let strength = 0;
    const requirements = {
        'req-length': password.length >= 8,
        'req-uppercase': /[A-Z]/.test(password),
        'req-lowercase': /[a-z]/.test(password),
        'req-number': /[0-9]/.test(password),
        'req-special': /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(req => {
        const element = document.getElementById(req);
        const icon = element.querySelector('i');
        if (requirements[req]) {
            element.style.color = '#22c55e';
            icon.classList.remove('fa-circle');
            icon.classList.add('fa-check-circle');
            strength += 20;
        } else {
            element.style.color = 'var(--text-lighter)';
            icon.classList.remove('fa-check-circle');
            icon.classList.add('fa-circle');
        }
    });
    
    // Update strength bar
    strengthBar.style.width = strength + '%';
    
    if (strength < 40) {
        strengthBar.style.background = '#ef4444';
        strengthText.textContent = 'Weak password';
        strengthText.style.color = '#ef4444';
    } else if (strength < 80) {
        strengthBar.style.background = '#eab308';
        strengthText.textContent = 'Fair password';
        strengthText.style.color = '#eab308';
    } else {
        strengthBar.style.background = '#22c55e';
        strengthText.textContent = 'Strong password';
        strengthText.style.color = '#22c55e';
    }
});

// Password match checker
const passwordConfirmInput = document.getElementById('password_confirmation');
const passwordMatchDiv = document.getElementById('password-match');

passwordConfirmInput.addEventListener('input', function(e) {
    const password = passwordInput.value;
    const confirm = e.target.value;
    
    if (confirm.length > 0) {
        if (password === confirm) {
            passwordMatchDiv.innerHTML = '<i class="fas fa-check-circle" style="color: #22c55e; margin-right: 0.5rem;"></i><span style="color: #22c55e;">Passwords match</span>';
        } else {
            passwordMatchDiv.innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444; margin-right: 0.5rem;"></i><span style="color: #ef4444;">Passwords do not match</span>';
        }
    } else {
        passwordMatchDiv.innerHTML = '';
    }
});

// Form submission
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    const password = passwordInput.value;
    const confirm = passwordConfirmInput.value;
    
    if (password !== confirm) {
        e.preventDefault();
        passwordMatchDiv.innerHTML = '<span style="color: #ef4444;">Passwords do not match!</span>';
        passwordConfirmInput.focus();
        return false;
    }
    
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting Password...';
});
</script>
@endpush
@endsection
