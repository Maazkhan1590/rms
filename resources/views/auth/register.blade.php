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
</script>
@endsection
