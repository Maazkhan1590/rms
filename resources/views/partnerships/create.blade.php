@extends('layouts.public')

@section('title', 'Submit Partnership/MOU | Academic Research Portal')

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

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

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
                <h1>Submit Partnership/MOU</h1>
                <p>Fill in the details below to submit your partnership/MOU for approval</p>
            </div>
            <form id="partnershipForm" method="POST" action="{{ route('partnerships.store') }}" class="auth-form" enctype="multipart/form-data">
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
                    <label for="partner_organization">
                        Partner Organization <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="partner_organization" name="partner_organization" required 
                           placeholder="e.g., Sultan Qaboos University"
                           pattern="[A-Za-z0-9 \\.,&'()\\/\\-]{2,255}"
                           title="Use letters/numbers and common punctuation only"
                           value="{{ old('partner_organization') }}">
                    @error('partner_organization')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">
                        Type <span style="color: var(--danger);">*</span>
                    </label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="mou" {{ old('type') == 'mou' ? 'selected' : '' }}>MOU (Memorandum of Understanding)</option>
                        <option value="moa" {{ old('type') == 'moa' ? 'selected' : '' }}>MOA (Memorandum of Agreement)</option>
                        <option value="project" {{ old('type') == 'project' ? 'selected' : '' }}>Project</option>
                        <option value="industry" {{ old('type') == 'industry' ? 'selected' : '' }}>Industry</option>
                    </select>
                    @error('type')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_signed">Date Signed</label>
                            <input type="date" class="form-control" id="date_signed" name="date_signed" 
                                   value="{{ old('date_signed') }}">
                            @error('date_signed')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                                   value="{{ old('expiry_date') }}">
                            @error('expiry_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="scope_theme">Scope/Theme</label>
                    <textarea class="form-control" id="scope_theme" name="scope_theme" rows="4"
                              placeholder="Describe the scope and theme of the partnership">{{ old('scope_theme') }}</textarea>
                    @error('scope_theme')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="lead_staff_id">Lead Staff</label>
                    <select class="form-control" id="lead_staff_id" name="lead_staff_id">
                        <option value="">Select Lead Staff (Optional)</option>
                        @foreach(\App\Models\User::whereHas('roles', function($q) { $q->where('title', 'Faculty'); })->get() as $user)
                            <option value="{{ $user->id }}" {{ old('lead_staff_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('lead_staff_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="outputs_papers_grants_events">Outputs (Papers, Grants, Events)</label>
                    <textarea class="form-control" id="outputs_papers_grants_events" name="outputs_papers_grants_events" rows="4"
                              placeholder="Describe outputs such as papers, grants, events resulting from this partnership">{{ old('outputs_papers_grants_events') }}</textarea>
                    @error('outputs_papers_grants_events')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="evidence_link">Evidence Link <span style="color: var(--danger);">*</span></label>
                    <input type="url" class="form-control" id="evidence_link" name="evidence_link" required
                           placeholder="https://example.com/evidence"
                           value="{{ old('evidence_link') }}">
                    <small class="form-text text-muted">Link to evidence document (required)</small>
                    @error('evidence_link')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="evidence_description">Evidence Description</label>
                    <textarea class="form-control" id="evidence_description" name="evidence_description" rows="3"
                              placeholder="Provide a description of the evidence">{{ old('evidence_description') }}</textarea>
                    @error('evidence_description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Evidence Upload Section -->
                <div class="form-group">
                    <label for="evidence_files">Evidence (URLs, Images, PDFs, etc.)</label>
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Upload multiple files (hold Ctrl/Cmd to select multiple) or add URLs as evidence. Supported formats: PDF, Images (JPG, PNG), Word documents, ZIP files.
                    </p>
                    
                    <!-- File Upload -->
                    <div class="evidence-upload-area" id="evidenceUploadArea">
                        <input type="file" id="evidence_files" name="evidence_files[]" 
                               multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.zip,image/*,application/pdf"
                               style="display: none;" onchange="handleEvidenceFiles(this)">
                        <button type="button" class="btn btn-outline" onclick="document.getElementById('evidence_files').click()" style="width: 100%; margin-bottom: 1rem; background: white; border: 2px solid var(--border-color); color: var(--text-color); padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.95rem; font-weight: 500; transition: all 0.3s ease;">
                            <i class="fas fa-upload"></i> Select Files (Multiple Selection Allowed)
                        </button>
                        <div id="evidenceFilesList" class="evidence-files-list"></div>
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
                    <label for="sdg_s">SDG Numbers (Comma-separated)</label>
                    <input type="text" class="form-control" id="sdg_s" name="sdg_s"
                           placeholder="e.g., 1,3,4,7"
                           value="{{ old('sdg_s') }}">
                    <small class="form-text text-muted">Enter SDG numbers separated by commas</small>
                    @error('sdg_s')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" class="form-control" id="year" name="year" 
                           min="1900" max="{{ date('Y') }}" 
                           value="{{ old('year', date('Y')) }}">
                    @error('year')
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
    // Evidence Files Handling
    let evidenceFileCount = 0;
    
    function handleEvidenceFiles(input) {
        const files = Array.from(input.files);
        const container = document.getElementById('evidenceFilesList');
        const fileCountInfo = document.getElementById('fileCountInfo');
        const fileCountText = document.getElementById('fileCountText');
        
        // Clear existing items
        container.innerHTML = '';
        
        if (files.length === 0) {
            fileCountInfo.style.display = 'none';
            return;
        }
        
        // Show file count
        fileCountInfo.style.display = 'block';
        fileCountText.textContent = `${files.length} file${files.length > 1 ? 's' : ''} selected`;
        
        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'evidence-file-item';
            fileItem.dataset.fileIndex = index;
            
            const fileIcon = file.type.startsWith('image/') ? 'fa-image' : (file.type === 'application/pdf' ? 'fa-file-pdf' : 'fa-file');
            
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
        });
    }
    
    function removeEvidenceFile(button) {
        const fileItem = button.closest('.evidence-file-item');
        const fileIndex = parseInt(fileItem.dataset.fileIndex);
        
        // Remove from file input
        const fileInput = document.getElementById('evidence_files');
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        
        files.forEach((file, index) => {
            if (index !== fileIndex) {
                dt.items.add(file);
            }
        });
        
        fileInput.files = dt.files;
        
        // Re-render the file list
        handleEvidenceFiles(fileInput);
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
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateEvidenceUrlButtons();
    });
</script>
@endsection
