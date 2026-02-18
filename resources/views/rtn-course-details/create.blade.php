@extends('layouts.public')

@section('title', 'Submit RTN Course Detail | Academic Research Portal')

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
    .excel-upload-section {
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
    }
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Submit RTN Course Detail</h1>
                <p>Fill in the details below to submit your RTN course detail for approval</p>
            </div>

            @auth
                @if(auth()->user()->hasRole(['Research Coordinator', 'Dean', 'Research Director']))
                <div class="excel-upload-section">
                    <h3 style="margin-bottom: 1rem;">
                        <i class="fas fa-file-excel" style="color: #059669;"></i> Bulk Upload via Excel
                    </h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">
                        Upload multiple RTN course details at once using an Excel file. 
                        Expected format: Course Code | Course Name | RTN Type | Year | Notes
                    </p>
                    <form method="POST" action="{{ route('rtn-course-details.upload-excel') }}" enctype="multipart/form-data" style="display: inline-block;">
                        @csrf
                        <div style="display: flex; gap: 1rem; align-items: center; justify-content: center; flex-wrap: wrap;">
                            <input type="file" name="excel_file" accept=".xlsx,.xls" required style="padding: 0.5rem;">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Upload Excel
                            </button>
                        </div>
                    </form>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #d1d5db;">
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                            <strong>OR</strong> submit individual course details using the form below
                        </p>
                    </div>
                </div>
                @endif
            @endauth

            <form method="POST" action="{{ route('rtn-course-details.store') }}" class="auth-form" enctype="multipart/form-data">
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="course_code">
                                Course Code <span style="color: var(--danger);">*</span>
                            </label>
                            <input type="text" class="form-control" id="course_code" name="course_code" required 
                                   value="{{ old('course_code') }}" maxlength="50">
                            @error('course_code')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rtn_type">
                                RTN Type <span style="color: var(--danger);">*</span>
                            </label>
                            <select class="form-control" id="rtn_type" name="rtn_type" required>
                                <option value="">Select RTN Type</option>
                                <option value="RTN_3" {{ old('rtn_type') == 'RTN_3' ? 'selected' : '' }}>RTN 3</option>
                                <option value="RTN_4" {{ old('rtn_type') == 'RTN_4' ? 'selected' : '' }}>RTN 4</option>
                                <option value="RTN_5" {{ old('rtn_type') == 'RTN_5' ? 'selected' : '' }}>RTN 5</option>
                                <option value="RTN_6" {{ old('rtn_type') == 'RTN_6' ? 'selected' : '' }}>RTN 6</option>
                                <option value="other" {{ old('rtn_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('rtn_type')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="course_name">
                        Course Name <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="course_name" name="course_name" required 
                           value="{{ old('course_name') }}" maxlength="255">
                    @error('course_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" class="form-control" id="year" name="year" 
                           min="1900" max="{{ date('Y') }}" value="{{ old('year', date('Y')) }}">
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
