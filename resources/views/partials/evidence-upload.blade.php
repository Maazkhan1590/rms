{{-- Reusable Evidence Upload Section --}}
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
