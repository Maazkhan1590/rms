{{-- Reusable Evidence Upload JavaScript --}}
<script>
    // Evidence Files Handling
    let evidenceFileCount = 0;
    
    function handleEvidenceFiles(input) {
        const files = Array.from(input.files);
        const container = document.getElementById('evidenceFilesList');
        const fileCountInfo = document.getElementById('fileCountInfo');
        const fileCountText = document.getElementById('fileCountText');
        
        // Clear existing items
        if (container) container.innerHTML = '';
        
        if (files.length === 0) {
            if (fileCountInfo) fileCountInfo.style.display = 'none';
            return;
        }
        
        // Show file count
        if (fileCountInfo) {
            fileCountInfo.style.display = 'block';
            if (fileCountText) fileCountText.textContent = `${files.length} file${files.length > 1 ? 's' : ''} selected`;
        }
        
        if (container) {
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
    }
    
    function removeEvidenceFile(button) {
        const fileItem = button.closest('.evidence-file-item');
        const fileIndex = parseInt(fileItem.dataset.fileIndex);
        
        // Remove from file input
        const fileInput = document.getElementById('evidence_files');
        if (!fileInput) return;
        
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
        if (!container) return;
        
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
        if (urlItem) urlItem.remove();
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
