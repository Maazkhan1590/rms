@extends('layouts.public')

@section('title', 'Submit RTN | Academic Research Portal')

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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-error::before {
        content: '⚠';
        font-size: 1rem;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    /* Evidence Upload Styles */
    .evidence-upload-area {
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        background: var(--light-color);
    }

    .evidence-files-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .evidence-file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        background: white;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .evidence-file-item .file-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
    }

    .evidence-file-item .file-icon {
        color: var(--primary-color);
    }

    .evidence-url-item {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .evidence-url-item:first-child .btn-danger {
        display: none !important;
    }
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Submit RTN (Research–Teaching Nexus)</h1>
                <p>Submit your research-teaching nexus contribution</p>
            </div>
            <form id="rtnForm" method="POST" action="{{ route('rtn-submissions.store') }}" class="auth-form" enctype="multipart/form-data">
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

                <div class="form-group">
                    <label for="rtn_type">
                        RTN Type <span style="color: var(--danger);">*</span>
                    </label>
                    <select class="form-control" id="rtn_type" name="rtn_type" required>
                        <option value="">Select RTN Type</option>
                        <option value="RTN-3" {{ old('rtn_type') == 'RTN-3' ? 'selected' : '' }}>RTN-3: Joint Publication with Students</option>
                        <option value="RTN-4" {{ old('rtn_type') == 'RTN-4' ? 'selected' : '' }}>RTN-4: Research Results Inform Teaching/Learning</option>
                    </select>
                    @error('rtn_type')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="title">
                        Title <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="title" name="title" required 
                           placeholder="Enter title of the work"
                           value="{{ old('title') }}">
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="year">
                        Year <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" class="form-control" id="year" name="year" 
                           required min="1900" max="{{ date('Y') }}" 
                           value="{{ old('year', date('Y')) }}">
                    @error('year')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="units">Units</label>
                    <input type="number" class="form-control" id="units" name="units" 
                           placeholder="Enter units" min="0"
                           value="{{ old('units') }}">
                    @error('units')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount_omr">Amount (OMR)</label>
                    <input type="number" step="0.01" class="form-control" id="amount_omr" name="amount_omr" 
                           placeholder="0.00" min="0"
                           value="{{ old('amount_omr') }}">
                    @error('amount_omr')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" 
                              placeholder="Provide a brief description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="evidence_description">Evidence Description</label>
                    <textarea class="form-control" id="evidence_description" name="evidence_description" rows="4" 
                              placeholder="Describe the evidence (e.g., student name listed as co-author, course file update, lecture material referencing research, etc.)">{{ old('evidence_description') }}</textarea>
                    <small style="color: var(--text-secondary); font-size: 0.875rem;">
                        For RTN-3: Student name listed as co-author in the paper.<br>
                        For RTN-4: Course file update, lecture material referencing research, assessment redesign, documented case study, etc.
                    </small>
                    @error('evidence_description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Evidence Upload Section -->
                <div class="form-group">
                    <label for="evidence_files">Evidence (URLs, Images, PDFs, etc.)</label>
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                        Upload multiple files or add URLs as evidence. Supported formats: PDF, Images (JPG, PNG), or URLs.
                    </p>
                    
                    <!-- File Upload -->
                    <div class="evidence-upload-area" id="evidenceUploadArea">
                        <input type="file" id="evidence_files" name="evidence_files[]" 
                               multiple accept=".pdf,.jpg,.jpeg,.png,.gif,image/*,application/pdf"
                               style="display: none;" onchange="handleEvidenceFiles(this)">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('evidence_files').click()" style="width: 100%; margin-bottom: 1rem;">
                            <i class="fas fa-upload"></i> Upload Files
                        </button>
                        <div id="evidenceFilesList" class="evidence-files-list"></div>
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
                        <button type="button" class="btn btn-outline btn-sm" onclick="addEvidenceUrl()" style="margin-top: 0.5rem;">
                            <i class="fas fa-plus"></i> Add Another URL
                        </button>
                    </div>
                    
                    @error('evidence_files')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    @error('evidence_urls')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2.5rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i> Submit RTN
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    // Evidence Files Handling
    let evidenceFileCount = 0;
    
    function handleEvidenceFiles(input) {
        const files = Array.from(input.files);
        const container = document.getElementById('evidenceFilesList');
        
        files.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'evidence-file-item';
            fileItem.dataset.fileIndex = evidenceFileCount;
            
            const fileIcon = file.type.startsWith('image/') ? 'fa-image' : 'fa-file-pdf';
            
            fileItem.innerHTML = `
                <div class="file-info">
                    <i class="fas ${fileIcon} file-icon"></i>
                    <span>${file.name}</span>
                    <small style="color: var(--text-secondary);">(${(file.size / 1024).toFixed(2)} KB)</small>
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeEvidenceFile(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(fileItem);
            evidenceFileCount++;
        });
    }
    
    function removeEvidenceFile(button) {
        const fileItem = button.closest('.evidence-file-item');
        const fileIndex = fileItem.dataset.fileIndex;
        
        // Remove from file input
        const fileInput = document.getElementById('evidence_files');
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        
        files.forEach((file, index) => {
            if (index != fileIndex) {
                dt.items.add(file);
            }
        });
        
        fileInput.files = dt.files;
        fileItem.remove();
    }
    
    // Evidence URLs Handling
    function addEvidenceUrl() {
        const container = document.getElementById('evidenceUrlsContainer');
        const urlItem = document.createElement('div');
        urlItem.className = 'evidence-url-item';
        urlItem.style.display = 'flex';
        urlItem.style.gap = '0.5rem';
        urlItem.style.marginBottom = '0.5rem';
        
        urlItem.innerHTML = `
            <input type="url" class="form-control evidence-url-input" 
                   name="evidence_urls[]" 
                   placeholder="https://...">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeEvidenceUrl(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        container.appendChild(urlItem);
        updateEvidenceUrlButtons();
    }
    
    function removeEvidenceUrl(button) {
        const urlItem = button.closest('.evidence-url-item');
        urlItem.remove();
        updateEvidenceUrlButtons();
    }
    
    function updateEvidenceUrlButtons() {
        const urlItems = document.querySelectorAll('.evidence-url-item');
        urlItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.btn-danger');
            if (removeBtn) {
                removeBtn.style.display = index === 0 ? 'none' : 'block';
            }
        });
    }
</script>
@endsection
