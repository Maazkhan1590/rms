@extends('layouts.auth')

@section('title', 'Register - Research Management System')

@section('content')
<div class="auth-container" style="max-width: 600px;">
    <div class="auth-card">
        <!-- SU Branding Header -->
        <div class="auth-header text-center mb-4">
            <div class="su-logo mb-3">
                <img src="{{ asset('images/su-logo.png') }}" alt="RMS Logo" class="img-fluid" style="max-width: 60px;"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22%230056b3%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2230%22 font-weight=%22bold%22>SU</text></svg>';">
            </div>
            <h4 class="fw-bold text-primary mb-1">Faculty Registration</h4>
            <p class="text-muted small">Research Management System</p>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps mb-4">
            <div class="progress-step active" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-label">Personal Info</div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-label">Academic Info</div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-label">Verification</div>
            </div>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" id="registrationForm" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Personal Information -->
            <div class="step-content" id="step1">
                <h5 class="mb-3 fw-semibold">Personal Information</h5>

                <div class="form-floating mb-3">
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           placeholder="Full Name" 
                           value="{{ old('name') }}"
                           required>
                    <label for="name"><i class="bi bi-person me-2"></i>Full Name</label>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           placeholder="Email" 
                           value="{{ old('email') }}"
                           required>
                    <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" 
                           class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" 
                           name="phone" 
                           placeholder="Phone" 
                           value="{{ old('phone') }}"
                           required>
                    <label for="phone"><i class="bi bi-telephone me-2"></i>Phone Number</label>
                    <div class="invalid-feedback"></div>
                </div>

                <button type="button" class="btn btn-primary w-100" onclick="nextStep(1)">
                    Next <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>

            <!-- Step 2: Academic Information -->
            <div class="step-content d-none" id="step2">
                <h5 class="mb-3 fw-semibold">Academic Information</h5>

                <div class="form-floating mb-3">
                    <select class="form-select @error('department') is-invalid @enderror" 
                            id="department" 
                            name="department" 
                            required>
                        <option value="">Select Department</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Mathematics">Mathematics</option>
                        <option value="Physics">Physics</option>
                        <option value="Chemistry">Chemistry</option>
                        <option value="English">English</option>
                        <option value="Economics">Economics</option>
                        <option value="Education">Education</option>
                        <option value="Business Administration">Business Administration</option>
                    </select>
                    <label for="department"><i class="bi bi-building me-2"></i>Department</label>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select @error('designation') is-invalid @enderror" 
                            id="designation" 
                            name="designation" 
                            required>
                        <option value="">Select Designation</option>
                        <option value="Professor">Professor</option>
                        <option value="Associate Professor">Associate Professor</option>
                        <option value="Assistant Professor">Assistant Professor</option>
                        <option value="Lecturer">Lecturer</option>
                        <option value="Research Fellow">Research Fellow</option>
                    </select>
                    <label for="designation"><i class="bi bi-award me-2"></i>Designation</label>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" 
                           class="form-control @error('employee_id') is-invalid @enderror" 
                           id="employee_id" 
                           name="employee_id" 
                           placeholder="Employee ID" 
                           value="{{ old('employee_id') }}"
                           required>
                    <label for="employee_id"><i class="bi bi-card-text me-2"></i>Employee ID</label>
                    <div class="invalid-feedback"></div>
                </div>

                <hr class="my-3">
                <p class="small text-muted mb-2"><i class="bi bi-info-circle me-1"></i>Optional: Research Profile Links</p>

                <div class="form-floating mb-3">
                    <input type="text" 
                           class="form-control @error('orcid') is-invalid @enderror" 
                           id="orcid" 
                           name="orcid" 
                           placeholder="ORCID" 
                           value="{{ old('orcid') }}">
                    <label for="orcid"><i class="bi bi-person-badge me-2"></i>ORCID (e.g., 0000-0002-1234-5678)</label>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-floating mb-3">
                    <input type="url" 
                           class="form-control @error('google_scholar') is-invalid @enderror" 
                           id="google_scholar" 
                           name="google_scholar" 
                           placeholder="Google Scholar" 
                           value="{{ old('google_scholar') }}">
                    <label for="google_scholar"><i class="bi bi-google me-2"></i>Google Scholar Profile URL</label>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-floating mb-3">
                    <input type="url" 
                           class="form-control @error('research_gate') is-invalid @enderror" 
                           id="research_gate" 
                           name="research_gate" 
                           placeholder="ResearchGate" 
                           value="{{ old('research_gate') }}">
                    <label for="research_gate"><i class="bi bi-search me-2"></i>ResearchGate Profile URL</label>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-fill" onclick="previousStep(2)">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </button>
                    <button type="button" class="btn btn-primary flex-fill" onclick="nextStep(2)">
                        Next <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Verification & Password -->
            <div class="step-content d-none" id="step3">
                <h5 class="mb-3 fw-semibold">Account Verification</h5>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        <i class="bi bi-lock me-2"></i>Create Password
                    </label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password"
                           required>
                    <div class="password-strength mt-2">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrength" role="progressbar"></div>
                        </div>
                        <small class="text-muted" id="strengthText">Password strength</small>
                    </div>
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i>Minimum 8 characters with uppercase, lowercase, numbers, and symbols
                    </small>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">
                        <i class="bi bi-lock-fill me-2"></i>Confirm Password
                    </label>
                    <input type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           required>
                    <div class="invalid-feedback"></div>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <label for="credentials" class="form-label fw-semibold required">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Upload Credentials (PDF only)
                    </label>
                    <input type="file" 
                           class="form-control @error('credentials') is-invalid @enderror" 
                           id="credentials" 
                           name="credentials" 
                           accept=".pdf"
                           required>
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i>Upload your degree or appointment letter (Max 2MB)
                    </small>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="profile_photo" class="form-label fw-semibold">
                        <i class="bi bi-image me-2"></i>Profile Photo (Optional)
                    </label>
                    <input type="file" 
                           class="form-control @error('profile_photo') is-invalid @enderror" 
                           id="profile_photo" 
                           name="profile_photo" 
                           accept="image/*">
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i>JPG, PNG (Max 1MB)
                    </small>
                    <div class="invalid-feedback"></div>
                    <div id="photoPreview" class="mt-2"></div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label small" for="terms">
                        I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <div class="alert alert-info small" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    Your account will be pending approval. You'll receive an email once approved by the administrator.
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-fill" onclick="previousStep(3)">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </button>
                    <button type="submit" class="btn btn-primary flex-fill" id="submitBtn">
                        <i class="bi bi-check-circle me-2"></i>Complete Registration
                    </button>
                </div>
            </div>
        </form>

        <!-- Login Link -->
        <div class="text-center mt-4">
            <p class="text-muted small mb-0">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                    Sign in here
                </a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStep = 1;

// Navigation functions
function nextStep(step) {
    if (validateStep(step)) {
        currentStep = step + 1;
        updateStepDisplay();
    }
}

function previousStep(step) {
    currentStep = step - 1;
    updateStepDisplay();
}

function updateStepDisplay() {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
    
    // Show current step
    document.getElementById('step' + currentStep).classList.remove('d-none');
    
    // Update progress indicators
    document.querySelectorAll('.progress-step').forEach((el, index) => {
        el.classList.remove('active', 'completed');
        if (index + 1 < currentStep) {
            el.classList.add('completed');
            el.querySelector('.step-circle').innerHTML = '<i class="bi bi-check"></i>';
        } else if (index + 1 === currentStep) {
            el.classList.add('active');
            el.querySelector('.step-circle').textContent = index + 1;
        } else {
            el.querySelector('.step-circle').textContent = index + 1;
        }
    });

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Validation function
function validateStep(step) {
    const stepElement = document.getElementById('step' + step);
    const inputs = stepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            showError(input, input.placeholder + ' is required');
            isValid = false;
        } else if (input.type === 'email' && !isValidEmail(input.value)) {
            showError(input, 'Please enter a valid email address');
            isValid = false;
        } else {
            clearError(input);
        }
    });

    // Additional validation for step 3
    if (step === 3) {
        const password = document.getElementById('password');
        const confirm = document.getElementById('password_confirmation');
        
        if (password.value.length < 8) {
            showError(password, 'Password must be at least 8 characters');
            isValid = false;
        } else if (password.value !== confirm.value) {
            showError(confirm, 'Passwords do not match');
            isValid = false;
        }
    }

    return isValid;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showError(input, message) {
    input.classList.add('is-invalid');
    const feedback = input.parentElement.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

function clearError(input) {
    input.classList.remove('is-invalid');
}

// Password strength meter
document.getElementById('password')?.addEventListener('input', function(e) {
    const password = e.target.value;
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (password.match(/[a-z]+/)) strength += 25;
    if (password.match(/[A-Z]+/)) strength += 25;
    if (password.match(/[0-9]+/)) strength += 12.5;
    if (password.match(/[$@#&!]+/)) strength += 12.5;
    
    const bar = document.getElementById('passwordStrength');
    const text = document.getElementById('strengthText');
    
    bar.style.width = strength + '%';
    
    if (strength < 50) {
        bar.className = 'progress-bar bg-danger';
        text.textContent = 'Weak password';
    } else if (strength < 75) {
        bar.className = 'progress-bar bg-warning';
        text.textContent = 'Fair password';
    } else {
        bar.className = 'progress-bar bg-success';
        text.textContent = 'Strong password';
    }
});

// Profile photo preview
document.getElementById('profile_photo')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photoPreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
});

// Form submission
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating Account...';
});

// Clear errors on input
document.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('input', () => clearError(input));
});
</script>
@endpush
@endsection
