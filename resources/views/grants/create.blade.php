@extends('layouts.base')

@section('title', 'Submit Grant - RMS')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #0056b3;
        --primary-dark: #003d82;
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --bg-white: #ffffff;
        --bg-light: #f7fafc;
        --border: #e2e8f0;
        --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .grant-create-page {
        min-height: 100vh;
        background: linear-gradient(180deg, #ffffff 0%, #f7fafc 100%);
        padding-top: 100px;
        padding-bottom: 4rem;
    }

    .page-header {
        text-align: center;
        margin-bottom: 3rem;
        padding: 0 2rem;
    }

    .page-badge {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.25rem, 4vw, 3rem);
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }

    .form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.1);
    }

    .btn-submit {
        background: var(--primary);
        color: white;
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background: #fee;
        color: #c33;
        border: 1px solid #fcc;
    }
</style>

<div class="grant-create-page">
    <div class="page-header">
        <span class="page-badge">
            <i class="fas fa-money-bill-wave"></i> Grant Submission
        </span>
        <h1 class="page-title">Submit Your Grant</h1>
        <p class="page-subtitle">Fill in the details below to submit your grant for approval</p>
    </div>

    <div class="form-container">
        <div class="form-card">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('grants.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="title">Grant Title *</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="grant_type">Grant Type *</label>
                    <select class="form-control" id="grant_type" name="grant_type" required>
                        <option value="">Select Grant Type</option>
                        <option value="external_grant" {{ old('grant_type') == 'external_grant' ? 'selected' : '' }}>External Grant / Consultancy</option>
                        <option value="external_matching_grant" {{ old('grant_type') == 'external_matching_grant' ? 'selected' : '' }}>External Matching Grant (MoA)</option>
                        <option value="grg_urg_advisor" {{ old('grant_type') == 'grg_urg_advisor' ? 'selected' : '' }}>GRG/URG as Advisor/Mentor</option>
                        <option value="patent_copyright" {{ old('grant_type') == 'patent_copyright' ? 'selected' : '' }}>Patent/Copyright</option>
                        <option value="grant_application" {{ old('grant_type') == 'grant_application' ? 'selected' : '' }}>Grant Application (Made)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Your Role *</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="PI" {{ old('role') == 'PI' ? 'selected' : '' }}>PI (Principal Investigator)</option>
                        <option value="Co-PI" {{ old('role') == 'Co-PI' ? 'selected' : '' }}>Co-PI</option>
                        <option value="Co-I" {{ old('role') == 'Co-I' ? 'selected' : '' }}>Co-I</option>
                        <option value="Advisor" {{ old('role') == 'Advisor' ? 'selected' : '' }}>Advisor</option>
                        <option value="Mentor" {{ old('role') == 'Mentor' ? 'selected' : '' }}>Mentor</option>
                        <option value="Applicant" {{ old('role') == 'Applicant' ? 'selected' : '' }}>Applicant</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sponsor_name">Sponsor Name</label>
                    <input type="text" class="form-control" id="sponsor_name" name="sponsor_name" value="{{ old('sponsor_name') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="amount_omr">Amount (OMR)</label>
                    <input type="number" step="0.01" class="form-control" id="amount_omr" name="amount_omr" value="{{ old('amount_omr') }}" min="0">
                    <small style="color: var(--text-secondary);">Up to 10,000 OMR = 1 project unit</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="award_year">Award Year *</label>
                    <input type="number" class="form-control" id="award_year" name="award_year" value="{{ old('award_year', date('Y')) }}" min="1900" max="{{ date('Y') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reference_code">Reference Code</label>
                    <input type="text" class="form-control" id="reference_code" name="reference_code" value="{{ old('reference_code') }}">
                </div>

                <div class="form-group" id="matching_grant_group" style="display: none;">
                    <label class="form-label" for="matching_grant_moa">Matching Grant MoA Reference</label>
                    <input type="text" class="form-control" id="matching_grant_moa" name="matching_grant_moa" value="{{ old('matching_grant_moa') }}">
                </div>

                <div class="form-group" id="patent_group" style="display: none;">
                    <label class="form-label" for="patent_registration_number">Patent Registration Number</label>
                    <input type="text" class="form-control" id="patent_registration_number" name="patent_registration_number" value="{{ old('patent_registration_number') }}">
                    <div style="margin-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="patent_su_registered" value="1" {{ old('patent_su_registered') ? 'checked' : '' }}>
                            <span>SU Registered</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="summary">Summary</label>
                    <textarea class="form-control" id="summary" name="summary" rows="4">{{ old('summary') }}</textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit Grant
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('grant_type').addEventListener('change', function() {
        const matchingGrantGroup = document.getElementById('matching_grant_group');
        const patentGroup = document.getElementById('patent_group');
        
        if (this.value === 'external_matching_grant') {
            matchingGrantGroup.style.display = 'block';
            patentGroup.style.display = 'none';
        } else if (this.value === 'patent_copyright') {
            matchingGrantGroup.style.display = 'none';
            patentGroup.style.display = 'block';
        } else {
            matchingGrantGroup.style.display = 'none';
            patentGroup.style.display = 'none';
        }
    });

    // Trigger on page load if value is already set
    document.getElementById('grant_type').dispatchEvent(new Event('change'));
</script>
@endsection
