@extends('layouts.public')

@section('title', 'Submit Student Involvement | Academic Research Portal')

@section('content')
<style>
    .auth-container {
        max-width: 900px !important;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-error {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Submit Student Involvement</h1>
                <p>Fill in the details below to submit your student involvement record for approval</p>
            </div>
            <form method="POST" action="{{ route('student-involvements.store') }}" class="auth-form" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                    <div style="background: #fee; color: #c33; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 0.5rem 0 0 1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="category">
                        Category <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="category" name="category" required 
                           value="{{ old('category') }}" placeholder="e.g., Mentoring, Tutoring, Research Assistant">
                    @error('category')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="count">
                                Count <span style="color: var(--danger);">*</span>
                            </label>
                            <input type="number" class="form-control" id="count" name="count" required 
                                   min="1" value="{{ old('count') }}">
                            @error('count')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="{{ old('date') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="academic_year">Academic Year</label>
                    <input type="text" class="form-control" id="academic_year" name="academic_year"
                           value="{{ old('academic_year') }}" placeholder="e.g., 2023-2024">
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="evidence_link">Evidence Link</label>
                    <input type="url" class="form-control" id="evidence_link" name="evidence_link"
                           value="{{ old('evidence_link') }}">
                </div>

                <div class="form-group">
                    <label for="evidence_description">Evidence Description</label>
                    <textarea class="form-control" id="evidence_description" name="evidence_description" rows="3">{{ old('evidence_description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Evidence Files</label>
                    <input type="file" class="form-control-file" name="evidence_files[]" multiple accept=".pdf,.doc,.docx,.zip">
                </div>

                <div class="form-group">
                    <label>Additional Evidence URLs</label>
                    <div id="evidence-urls-container">
                        <input type="url" class="form-control mb-2" name="evidence_urls[]" placeholder="https://example.com/evidence">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-evidence-url">
                        <i class="fa fa-plus"></i> Add Another URL
                    </button>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-save"></i> Save as Draft
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-evidence-url').addEventListener('click', function() {
        const container = document.getElementById('evidence-urls-container');
        const newField = document.createElement('div');
        newField.className = 'mb-2';
        newField.innerHTML = `
            <input type="url" class="form-control" name="evidence_urls[]" placeholder="https://example.com/evidence">
            <button type="button" class="btn btn-sm btn-danger remove-url mt-1">
                <i class="fa fa-times"></i> Remove
            </button>
        `;
        container.appendChild(newField);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-url')) {
            e.target.closest('.mb-2').remove();
        }
    });
});
</script>
@endsection
