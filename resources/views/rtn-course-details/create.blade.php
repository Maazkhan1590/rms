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
                                   value="{{ old('course_code') }}" maxlength="50"
                                   placeholder="e.g., COMP1010"
                                   pattern="[A-Za-z0-9][A-Za-z0-9_\\-\\.\\/]{0,49}"
                                   title="Letters/numbers and _ - . / only">
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
                           value="{{ old('course_name') }}" maxlength="255"
                           placeholder="e.g., Introduction to Programming"
                           title="Avoid special/control characters">
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

                <!-- Evidence Upload Section -->
                <div class="form-group">
                    <label for="evidence_files">Evidence (URLs, Images, PDFs, etc.)</label>
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Upload multiple files (hold Ctrl/Cmd to select multiple) or add URLs as evidence. Supported formats: PDF, Images (JPG, PNG), Word documents, ZIP files.
                    </p>
                    
                    <!-- File Upload -->
                    <div class="evidence-upload-area" id="evidenceUploadArea" style="border: 2px dashed var(--border-color); border-radius: 12px; padding: 1.5rem; background: var(--light-color);">
                        <input type="file" id="evidence_files" name="evidence_files[]" 
                               multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.zip,image/*,application/pdf"
                               style="display: none;" onchange="handleEvidenceFiles(this)">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('evidence_files').click()" style="width: 100%; margin-bottom: 1rem; background: white; border: 2px solid var(--border-color); color: var(--text-color); padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.95rem; font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-upload"></i> Select Files (Multiple Selection Allowed)
                        </button>
                        <div id="evidenceFilesList" class="evidence-files-list" style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;"></div>
                        <div id="fileCountInfo" style="margin-top: 0.5rem; padding: 0.5rem; background: #e0f2fe; border-radius: 4px; display: none;">
                            <i class="fas fa-check-circle" style="color: #0284c7;"></i>
                            <span id="fileCountText" style="color: #0c4a6e; font-size: 0.875rem; font-weight: 500;"></span>
                        </div>
                    </div>
                    
                    <!-- URL Input -->
                    <div class="evidence-urls-section" style="margin-top: 1rem;">
                        <label style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; display: block;">Or add URLs:</label>
                        <div id="evidenceUrlsContainer">
                            <div class="evidence-url-item" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <input type="url" class="form-control evidence-url-input" 
                                       name="evidence_urls[]" 
                                       placeholder="https://...">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeEvidenceUrl(this)" style="display: none;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline btn-sm" onclick="addEvidenceUrl()" style="margin-top: 0.5rem; background: white; border: 2px solid var(--border-color); color: var(--text-color); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
                            <i class="fas fa-plus"></i> Add Another URL
                        </button>
                    </div>
                    
                    @error('evidence_files')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    @error('evidence_files.*')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    @error('evidence_urls')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
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

@include('partials.evidence-upload-scripts')
@endsection
