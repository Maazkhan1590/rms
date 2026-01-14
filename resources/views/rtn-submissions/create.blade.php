@extends('layouts.base')

@section('title', 'Submit RTN Submission - RMS')

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

    .rtn-create-page {
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

<div class="rtn-create-page">
    <div class="page-header">
        <span class="page-badge">
            <i class="fas fa-link"></i> RTN Submission
        </span>
        <h1 class="page-title">Submit RTN (Research–Teaching Nexus)</h1>
        <p class="page-subtitle">Submit your research-teaching nexus contribution</p>
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

            <form action="{{ route('rtn-submissions.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="rtn_type">RTN Type *</label>
                    <select class="form-control" id="rtn_type" name="rtn_type" required>
                        <option value="">Select RTN Type</option>
                        <option value="RTN-3" {{ old('rtn_type') == 'RTN-3' ? 'selected' : '' }}>RTN-3: Joint Publication with Students</option>
                        <option value="RTN-4" {{ old('rtn_type') == 'RTN-4' ? 'selected' : '' }}>RTN-4: Research Results Inform Teaching/Learning</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="title">Title *</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required placeholder="Enter title of the work">
                </div>

                <div class="form-group">
                    <label class="form-label" for="year">Year *</label>
                    <input type="number" class="form-control" id="year" name="year" value="{{ old('year', date('Y')) }}" min="1900" max="{{ date('Y') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Provide a brief description">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="evidence_description">Evidence Description</label>
                    <textarea class="form-control" id="evidence_description" name="evidence_description" rows="4" placeholder="Describe the evidence (e.g., student name listed as co-author, course file update, lecture material referencing research, etc.)">{{ old('evidence_description') }}</textarea>
                    <small style="color: var(--text-secondary);">
                        For RTN-3: Student name listed as co-author in the paper.<br>
                        For RTN-4: Course file update, lecture material referencing research, assessment redesign, documented case study, etc.
                    </small>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit RTN
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
