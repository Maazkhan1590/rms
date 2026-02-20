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

<style>
    /* jQuery Validate Error Styles - Red Color */
    .form-control.error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    label.error {
        color: #dc3545 !important;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
    }

    label.error::before {
        content: '⚠';
        font-size: 1rem;
        color: #dc3545 !important;
    }

    .form-error {
        color: #dc3545 !important;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }
</style>

<script>
    // jQuery Validate with Regular Expressions for Login Form
    $(document).ready(function() {
        // Load jQuery Validate library
        if (typeof $.fn.validate === 'undefined') {
            $.getScript('https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', function() {
                initializeLoginValidation();
            });
        } else {
            initializeLoginValidation();
        }
    });

    function initializeLoginValidation() {
        if (typeof window.jQuery === 'undefined') {
            console.error('jQuery is not available');
            return;
        }
        var $ = window.jQuery;
        
        // Add custom validation methods
        $.validator.addMethod("emailFormat", function(value, element) {
            return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }, "Please enter a valid email address.");

        $.validator.addMethod("passwordFormat", function(value, element) {
            if (this.optional(element)) return true;
            // Password should be at least 6 characters
            return value.length >= 6;
        }, "Password must be at least 6 characters long.");

        // Initialize validation
        $('#login-form').validate({
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                // For password field, insert after the password-input div
                if (element.attr('name') === 'password') {
                    error.insertAfter(element.closest('.password-input'));
                } else {
                    error.insertAfter(element);
                }
            },
            rules: {
                email: {
                    required: true,
                    emailFormat: true,
                    maxlength: 255
                },
                password: {
                    required: true,
                    passwordFormat: true,
                    minlength: 6
                }
            },
            messages: {
                email: {
                    required: "Email address is required.",
                    emailFormat: "Please enter a valid email address.",
                    maxlength: "Email address cannot exceed 255 characters."
                },
                password: {
                    required: "Password is required.",
                    passwordFormat: "Password must be at least 6 characters long.",
                    minlength: "Password must be at least 6 characters long."
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    }
</script>
@endsection
