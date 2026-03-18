@extends('layouts.public')

@section('title', 'Submit Editorial Appointment | Academic Research Portal')

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
                <h1>Submit Editorial Appointment</h1>
                <p>Fill in the details below to submit your editorial appointment for approval</p>
            </div>
            <form method="POST" action="{{ route('editorial-appointments.store') }}" class="auth-form" enctype="multipart/form-data">
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
                    <label for="journal_conference">
                        Journal/Conference Name <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="journal_conference" name="journal_conference" required 
                           placeholder="e.g., Nature, IEEE Access"
                           title="Avoid special/control characters"
                           value="{{ old('journal_conference') }}">
                    @error('journal_conference')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">
                        Role <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="role" name="role" required 
                           value="{{ old('role') }}" placeholder="e.g., Editor-in-Chief, Associate Editor, Reviewer">
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ old('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ old('end_date') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" class="form-control" id="year" name="year" 
                           min="1900" max="{{ date('Y') }}" value="{{ old('year', date('Y')) }}">
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Optional notes">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="evidence_link">Evidence Link</label>
                    <input type="url" class="form-control" id="evidence_link" name="evidence_link"
                           placeholder="https://example.com/evidence"
                           value="{{ old('evidence_link') }}">
                </div>

                <div class="form-group">
                    <label for="evidence_description">Evidence Description</label>
                    <textarea class="form-control" id="evidence_description" name="evidence_description" rows="3" placeholder="Briefly describe the evidence">{{ old('evidence_description') }}</textarea>
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
