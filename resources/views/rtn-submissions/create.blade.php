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

    /* jQuery Validate Error Styles - Red Color */
    .form-control.error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    label.error {
        color: #dc3545 !important;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: normal;
    }

    label.error::before {
        content: '⚠';
        font-size: 1rem;
        color: #dc3545 !important;
    }

    .form-error {
        color: #dc3545 !important;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
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
        const newFiles = Array.from(input.files);
        const container = document.getElementById('evidenceFilesList');
        const fileInput = document.getElementById('evidence_files');
        
        // Get existing files from the input
        const existingFiles = Array.from(fileInput.files);
        
        // Create a new DataTransfer to accumulate all files
        const dt = new DataTransfer();
        
        // Add existing files first
        existingFiles.forEach(file => dt.items.add(file));
        
        // Add new files
        newFiles.forEach(file => {
            dt.items.add(file);
            
            // Create display element
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
        
        // Update the file input with all accumulated files
        fileInput.files = dt.files;
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

    // jQuery Validate with Regular Expressions
    $(document).ready(function() {
        // Load jQuery Validate library
        if (typeof $.fn.validate === 'undefined') {
            $.getScript('https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', function() {
                initializeRTNValidation();
            });
        } else {
            initializeRTNValidation();
        }
    });

    function initializeRTNValidation() {
        if (typeof window.jQuery === 'undefined') {
            console.error('jQuery is not available');
            return;
        }
        var $ = window.jQuery;
        
        // Add custom validation methods
        $.validator.addMethod("rtnType", function(value, element) {
            return this.optional(element) || /^(RTN-3|RTN-4)$/.test(value);
        }, "Please select a valid RTN type (RTN-3 or RTN-4).");

        $.validator.addMethod("yearRange", function(value, element) {
            const year = parseInt(value);
            return this.optional(element) || (year >= 1900 && year <= new Date().getFullYear());
        }, "Please enter a valid year between 1900 and current year.");

        $.validator.addMethod("positiveNumber", function(value, element) {
            return this.optional(element) || /^\d+(\.\d{1,2})?$/.test(value) && parseFloat(value) >= 0;
        }, "Please enter a valid positive number (up to 2 decimal places).");

        $.validator.addMethod("validUrl", function(value, element) {
            if (this.optional(element)) return true;
            const urlPattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
            return urlPattern.test(value);
        }, "Please enter a valid URL.");

        // Initialize validation
        $('#rtnForm').validate({
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            rules: {
                rtn_type: {
                    required: true,
                    rtnType: true
                },
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 500,
                    pattern: /^[a-zA-Z0-9\s\-_.,;:()\[\]'"\/]+$/
                },
                year: {
                    required: true,
                    yearRange: true,
                    digits: true
                },
                units: {
                    positiveNumber: true,
                    min: 0
                },
                amount_omr: {
                    positiveNumber: true,
                    min: 0
                },
                description: {
                    maxlength: 2000
                },
                evidence_description: {
                    maxlength: 2000
                },
                'evidence_urls[]': {
                    validUrl: true
                }
            },
            messages: {
                rtn_type: {
                    required: "RTN Type is required.",
                    rtnType: "Please select a valid RTN type."
                },
                title: {
                    required: "Title is required.",
                    minlength: "Title must be at least 3 characters long.",
                    maxlength: "Title cannot exceed 500 characters.",
                    pattern: "Title contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed."
                },
                year: {
                    required: "Year is required.",
                    yearRange: "Please enter a valid year between 1900 and current year.",
                    digits: "Year must be a valid number."
                },
                units: {
                    positiveNumber: "Units must be a valid positive number.",
                    min: "Units cannot be negative."
                },
                amount_omr: {
                    positiveNumber: "Amount must be a valid positive number.",
                    min: "Amount cannot be negative."
                },
                description: {
                    maxlength: "Description cannot exceed 2000 characters."
                },
                evidence_description: {
                    maxlength: "Evidence description cannot exceed 2000 characters."
                },
                'evidence_urls[]': {
                    validUrl: "Please enter a valid URL."
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        // Validate dynamically added URL fields
        $(document).on('blur', '.evidence-url-input', function() {
            $(this).valid();
        });
    }
</script>
@endsection
