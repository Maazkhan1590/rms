@extends('layouts.public')

@section('title', 'Register | Academic Research Portal')

@section('content')
<!-- Register Section -->
<section class="auth-section">
    <div class="container">
        <div class="auth-container" style="max-width: 700px;">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Join our community of researchers and academics</p>
            </div>
            <form method="POST" action="{{ route('register') }}" id="register-form" class="auth-form">
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

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" value="{{ old('name') }}" required autofocus>
                        <div class="form-error" id="name-error"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="register-email">Email Address <span style="color: #ef4444;">*</span></label>
                    <input type="email" id="register-email" name="email" class="form-control" placeholder="Enter your email address" value="{{ old('email') }}" required>
                    <div class="form-error" id="register-email-error"></div>
                </div>
                <div class="form-group">
                    <label for="affiliation">Affiliation / Institution</label>
                    <input type="text" id="affiliation" name="affiliation" class="form-control" placeholder="University, Research Institute, etc." value="{{ old('affiliation') }}">
                    <div class="form-error" id="affiliation-error"></div>
                </div>
                <div class="form-group">
                    <label for="college_id">College</label>
                    <select id="college_id" name="college_id" class="form-control">
                        <option value="">Select College</option>
                        @foreach(\App\Models\College::where('is_active', true)->get() as $college)
                            <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>
                                {{ $college->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select id="department_id" name="department_id" class="form-control">
                        <option value="">Select Department</option>
                        @if(old('college_id'))
                            @foreach(\App\Models\Department::where('college_id', old('college_id'))->where('is_active', true)->get() as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label for="designation">Designation</label>
                    <input type="text" id="designation" name="designation" class="form-control" placeholder="e.g., Professor, Associate Professor" value="{{ old('designation') }}">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="register-password">Password <span style="color: #ef4444;">*</span></label>
                        <div class="password-input">
                            <input type="password" id="register-password" name="password" class="form-control" placeholder="Create a password" required>
                            <button type="button" class="toggle-password" id="toggle-register-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-error" id="register-password-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password <span style="color: #ef4444;">*</span></label>
                        <div class="password-input">
                            <input type="password" id="confirm-password" name="password_confirmation" class="form-control" placeholder="Confirm your password" required>
                            <button type="button" class="toggle-password" id="toggle-confirm-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-error" id="confirm-password-error"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="terms" name="terms" required>
                        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="newsletter" name="newsletter">
                        <span>Subscribe to our newsletter for updates and announcements</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>
            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
            </div>
        </div>
    </div>
</section>

<script>
    // Load departments when college is selected
    document.getElementById('college_id')?.addEventListener('change', function() {
        const collegeId = this.value;
        const departmentSelect = document.getElementById('department_id');
        
        // Clear existing options
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
        
        if (collegeId) {
            // Fetch departments for selected college
            fetch(`/api/v1/colleges/${collegeId}/departments`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        departmentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading departments:', error);
                });
        }
    });

    // jQuery Validate with Regular Expressions for Register Form
    // Wait for jQuery to be available
    function initRegisterValidation() {
        if (typeof window.jQuery === 'undefined' || !window.jQuery.fn) {
            setTimeout(initRegisterValidation, 100);
            return;
        }
        
        var $ = window.jQuery;
        
        $(document).ready(function() {
            // Load jQuery Validate library
            if (typeof $.fn.validate === 'undefined') {
                $.getScript('https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', function() {
                    initializeRegisterValidation();
                });
            } else {
                initializeRegisterValidation();
            }
        });
    }
    
    initRegisterValidation();

    function initializeRegisterValidation() {
        if (typeof window.jQuery === 'undefined') {
            console.error('jQuery is not available');
            return;
        }
        var $ = window.jQuery;
        
        // Add custom validation methods
        $.validator.addMethod("nameFormat", function(value, element) {
            return this.optional(element) || /^[a-zA-Z\s\-'\.]+$/.test(value) && value.trim().length >= 2;
        }, "Name should contain only letters, spaces, hyphens, apostrophes, and periods (minimum 2 characters).");

        $.validator.addMethod("emailFormat", function(value, element) {
            return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }, "Please enter a valid email address.");

        $.validator.addMethod("passwordStrength", function(value, element) {
            if (this.optional(element)) return true;
            // Password must be at least 8 characters with uppercase, lowercase, number, and special character
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
        }, "Password must be at least 8 characters with uppercase, lowercase, number, and special character.");

        $.validator.addMethod("alphanumericWithSpaces", function(value, element) {
            if (this.optional(element)) return true;
            return /^[a-zA-Z0-9\s\-_.,;:()\[\]'"\/]+$/.test(value);
        }, "This field contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed.");

        // Initialize validation
        $('#register-form').validate({
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                // For password fields, insert after the password-input div
                if (element.attr('name') === 'password' || element.attr('name') === 'password_confirmation') {
                    error.insertAfter(element.closest('.password-input'));
                } else {
                    error.insertAfter(element);
                }
            },
            rules: {
                name: {
                    required: true,
                    nameFormat: true,
                    minlength: 2,
                    maxlength: 255
                },
                email: {
                    required: true,
                    emailFormat: true,
                    maxlength: 255
                },
                affiliation: {
                    maxlength: 255,
                    alphanumericWithSpaces: true
                },
                designation: {
                    maxlength: 100,
                    alphanumericWithSpaces: true
                },
                password: {
                    required: true,
                    passwordStrength: true,
                    minlength: 8
                },
                password_confirmation: {
                    required: true,
                    equalTo: '#register-password',
                    minlength: 8
                },
                terms: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Full name is required.",
                    nameFormat: "Name should contain only letters, spaces, hyphens, apostrophes, and periods (minimum 2 characters).",
                    minlength: "Name must be at least 2 characters long.",
                    maxlength: "Name cannot exceed 255 characters."
                },
                email: {
                    required: "Email address is required.",
                    emailFormat: "Please enter a valid email address.",
                    maxlength: "Email address cannot exceed 255 characters."
                },
                affiliation: {
                    maxlength: "Affiliation cannot exceed 255 characters.",
                    alphanumericWithSpaces: "Affiliation contains invalid characters."
                },
                designation: {
                    maxlength: "Designation cannot exceed 100 characters.",
                    alphanumericWithSpaces: "Designation contains invalid characters."
                },
                password: {
                    required: "Password is required.",
                    passwordStrength: "Password must be at least 8 characters with uppercase, lowercase, number, and special character (@$!%*?&).",
                    minlength: "Password must be at least 8 characters long."
                },
                password_confirmation: {
                    required: "Please confirm your password.",
                    equalTo: "Passwords do not match.",
                    minlength: "Password must be at least 8 characters long."
                },
                terms: {
                    required: "You must agree to the Terms of Service and Privacy Policy."
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    }
</script>

<style>
    /* Form validation styles are in form-validation.css */
</style>
@endsection
