@extends('layouts.public')

@section('title', 'Submit Grant | Academic Research Portal')

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
                <h1>Submit Your Grant</h1>
                <p>Fill in the details below to submit your grant for approval</p>
            </div>
            <form id="grantForm" method="POST" action="{{ route('grants.store') }}" class="auth-form" enctype="multipart/form-data">
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
                    <label for="title">
                        Grant Title <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="title" name="title" required 
                           placeholder="Enter grant title"
                           value="{{ old('title') }}">
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="grant_type">
                        Grant Type <span style="color: var(--danger);">*</span>
                    </label>
                    <select class="form-control" id="grant_type" name="grant_type" required>
                        <option value="">Select Grant Type</option>
                        <option value="RG" {{ old('grant_type') == 'RG' ? 'selected' : '' }}>RG</option>
                        <option value="GRG" {{ old('grant_type') == 'GRG' ? 'selected' : '' }}>GRG</option>
                        <option value="URG" {{ old('grant_type') == 'URG' ? 'selected' : '' }}>URG</option>
                        <option value="EJAAD" {{ old('grant_type') == 'EJAAD' ? 'selected' : '' }}>EJAAD</option>
                        <option value="external_grant" {{ old('grant_type') == 'external_grant' ? 'selected' : '' }}>External Grant / Consultancy</option>
                        <option value="external_matching_grant" {{ old('grant_type') == 'external_matching_grant' ? 'selected' : '' }}>External Matching Grant (MoA)</option>
                        <option value="grg_urg_advisor" {{ old('grant_type') == 'grg_urg_advisor' ? 'selected' : '' }}>GRG/URG as Advisor/Mentor</option>
                        <option value="patent_copyright" {{ old('grant_type') == 'patent_copyright' ? 'selected' : '' }}>Patent/Copyright</option>
                        <option value="grant_application" {{ old('grant_type') == 'grant_application' ? 'selected' : '' }}>Grant Application (Made)</option>
                        <option value="other" {{ old('grant_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('grant_type')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">
                        Your Role <span style="color: var(--danger);">*</span>
                    </label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="PI" {{ old('role') == 'PI' ? 'selected' : '' }}>PI (Principal Investigator)</option>
                        <option value="Co-PI" {{ old('role') == 'Co-PI' ? 'selected' : '' }}>Co-PI</option>
                        <option value="Co-I" {{ old('role') == 'Co-I' ? 'selected' : '' }}>Co-I</option>
                        <option value="Advisor" {{ old('role') == 'Advisor' ? 'selected' : '' }}>Advisor</option>
                        <option value="Mentor" {{ old('role') == 'Mentor' ? 'selected' : '' }}>Mentor</option>
                        <option value="Applicant" {{ old('role') == 'Applicant' ? 'selected' : '' }}>Applicant</option>
                    </select>
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sponsor_name">Sponsor Name</label>
                    <input type="text" class="form-control" id="sponsor_name" name="sponsor_name" 
                           placeholder="Enter sponsor name"
                           value="{{ old('sponsor_name') }}">
                    @error('sponsor_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount_omr">Amount (OMR)</label>
                    <input type="number" step="0.01" class="form-control" id="amount_omr" name="amount_omr" 
                           placeholder="0.00" min="0"
                           value="{{ old('amount_omr') }}">
                    <small style="color: var(--text-secondary); font-size: 0.875rem;">Up to 10,000 OMR = 1 project unit</small>
                    @error('amount_omr')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="award_year">
                        Award Year <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="number" class="form-control" id="award_year" name="award_year" 
                           required min="1900" max="{{ date('Y') }}" 
                           value="{{ old('award_year', date('Y')) }}">
                    @error('award_year')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="grant_status">Grant Status</label>
                    <select class="form-control" id="grant_status" name="grant_status">
                        <option value="draft" {{ old('grant_status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ old('grant_status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="accepted" {{ old('grant_status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="ongoing" {{ old('grant_status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ old('grant_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('grant_status')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="application_date">Application Date</label>
                    <input type="date" class="form-control" id="application_date" name="application_date" 
                           value="{{ old('application_date') }}">
                    @error('application_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ old('start_date') }}">
                    @error('start_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ old('end_date') }}">
                    @error('end_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount_received_omr">Amount Received To Date (OMR)</label>
                    <input type="number" step="0.01" class="form-control" id="amount_received_omr" name="amount_received_omr" 
                           placeholder="0.00" min="0"
                           value="{{ old('amount_received_omr') }}">
                    @error('amount_received_omr')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="kt_income" value="1" {{ old('kt_income') ? 'checked' : '' }}>
                        KT Income? (Y/N)
                    </label>
                    @error('kt_income')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sdgs">SDG(s)</label>
                    <input type="text" class="form-control" id="sdgs" name="sdgs[]" 
                           placeholder="Enter SDG (e.g., SDG 1, SDG 3)"
                           value="{{ old('sdgs.0') }}">
                    <small style="color: var(--text-secondary); font-size: 0.875rem;">You can add multiple SDGs</small>
                    @error('sdgs')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reporting_period">Reporting Period</label>
                    <select class="form-control" id="reporting_period" name="reporting_period">
                        <option value="">Select Reporting Period</option>
                        <option value="Q1" {{ old('reporting_period') == 'Q1' ? 'selected' : '' }}>Q1</option>
                        <option value="Q2" {{ old('reporting_period') == 'Q2' ? 'selected' : '' }}>Q2</option>
                        <option value="Q3" {{ old('reporting_period') == 'Q3' ? 'selected' : '' }}>Q3</option>
                        <option value="Q4" {{ old('reporting_period') == 'Q4' ? 'selected' : '' }}>Q4</option>
                    </select>
                    @error('reporting_period')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" id="matching_grant_group" style="display: none;">
                    <label for="matching_grant_moa">Matching Grant MoA Reference</label>
                    <input type="text" class="form-control" id="matching_grant_moa" name="matching_grant_moa" 
                           placeholder="Enter MoA reference"
                           value="{{ old('matching_grant_moa') }}">
                    @error('matching_grant_moa')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" id="patent_group" style="display: none;">
                    <label for="patent_registration_number">Patent Registration Number</label>
                    <input type="text" class="form-control" id="patent_registration_number" name="patent_registration_number" 
                           placeholder="Enter patent registration number"
                           value="{{ old('patent_registration_number') }}">
                    <div style="margin-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="patent_su_registered" value="1" {{ old('patent_su_registered') ? 'checked' : '' }}>
                            <span>SU Registered</span>
                        </label>
                    </div>
                    @error('patent_registration_number')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reference_code">Reference Code</label>
                    <input type="text" class="form-control" id="reference_code" name="reference_code" 
                           placeholder="Enter reference code"
                           value="{{ old('reference_code') }}">
                    @error('reference_code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary">Summary</label>
                    <textarea class="form-control" id="summary" name="summary" rows="4" 
                              placeholder="Provide a brief summary of the grant...">{{ old('summary') }}</textarea>
                    @error('summary')
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
                        <i class="fas fa-paper-plane"></i> Submit Grant
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    // Grant Type Change Handler
    document.getElementById('grant_type')?.addEventListener('change', function() {
        const matchingGrantGroup = document.getElementById('matching_grant_group');
        const patentGroup = document.getElementById('patent_group');
        
        if (this.value === 'external_matching_grant') {
            matchingGrantGroup.style.display = 'block';
            patentGroup.style.display = 'none';
        } else if (this.value === 'patent_copyright') {
            matchingGrantGroup.style.display = 'none';
            patentGroup.style.display = 'block';
        } else {
            matchingGrantGroup.style.display = 'none';
            patentGroup.style.display = 'none';
        }
    });

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

    // Trigger on page load if value is already set
    document.getElementById('grant_type')?.dispatchEvent(new Event('change'));

    // jQuery Validate with Regular Expressions
    $(document).ready(function() {
        // Load jQuery Validate library
        if (typeof $.fn.validate === 'undefined') {
            $.getScript('https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', function() {
                initializeGrantValidation();
            });
        } else {
            initializeGrantValidation();
        }
    });

    function initializeGrantValidation() {
        if (typeof window.jQuery === 'undefined') {
            console.error('jQuery is not available');
            return;
        }
        var $ = window.jQuery;
        
        // Add custom validation methods
        $.validator.addMethod("grantType", function(value, element) {
            const validTypes = ['RG', 'GRG', 'URG', 'EJAAD', 'external_grant', 'external_matching_grant', 'grg_urg_advisor', 'patent_copyright', 'grant_application', 'other'];
            return this.optional(element) || validTypes.includes(value);
        }, "Please select a valid grant type.");

        $.validator.addMethod("role", function(value, element) {
            const validRoles = ['PI', 'Co-PI', 'Co-I', 'Advisor', 'Mentor', 'Applicant'];
            return this.optional(element) || validRoles.includes(value);
        }, "Please select a valid role.");

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

        $.validator.addMethod("titlePattern", function(value, element) {
            if (this.optional(element)) return true;
            return /^[a-zA-Z0-9\s\-_.,;:()\[\]'"\/]+$/.test(value);
        }, "Title contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed.");

        $.validator.addMethod("namePattern", function(value, element) {
            if (this.optional(element)) return true;
            return /^[a-zA-Z0-9\s\-_.,;:()\[\]'"\/]+$/.test(value);
        }, "Contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed.");

        $.validator.addMethod("patentPattern", function(value, element) {
            if (this.optional(element)) return true;
            return /^[A-Z0-9\-]+$/.test(value);
        }, "Patent registration number should contain only uppercase letters, numbers, and hyphens.");

        $.validator.addMethod("referencePattern", function(value, element) {
            if (this.optional(element)) return true;
            return /^[A-Z0-9\-_]+$/.test(value);
        }, "Reference code should contain only uppercase letters, numbers, hyphens, and underscores.");

        $.validator.addMethod("dateRange", function(value, element) {
            if (this.optional(element)) return true;
            const date = new Date(value);
            const minDate = new Date('1900-01-01');
            const maxDate = new Date();
            return date >= minDate && date <= maxDate;
        }, "Please enter a valid date.");

        $.validator.addMethod("sdgFormat", function(value, element) {
            if (this.optional(element)) return true;
            return /^SDG\s*\d{1,2}$/i.test(value.trim());
        }, "SDG format should be 'SDG 1' to 'SDG 17'.");

        // Initialize validation
        $('#grantForm').validate({
            errorClass: 'error',
            validClass: 'valid',
            errorElement: 'label',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            rules: {
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 500,
                    titlePattern: true
                },
                grant_type: {
                    required: true,
                    grantType: true
                },
                role: {
                    required: true,
                    role: true
                },
                sponsor_name: {
                    maxlength: 255,
                    namePattern: true
                },
                amount_omr: {
                    positiveNumber: true,
                    min: 0
                },
                award_year: {
                    required: true,
                    yearRange: true,
                    digits: true
                },
                grant_status: {
                    required: false
                },
                application_date: {
                    dateRange: true,
                    date: true
                },
                start_date: {
                    dateRange: true,
                    date: true
                },
                end_date: {
                    dateRange: true,
                    date: true
                },
                amount_received_omr: {
                    positiveNumber: true,
                    min: 0
                },
                'sdgs[]': {
                    sdgFormat: true
                },
                matching_grant_moa: {
                    maxlength: 255
                },
                patent_registration_number: {
                    maxlength: 255,
                    patentPattern: true
                },
                reference_code: {
                    maxlength: 100,
                    referencePattern: true
                },
                summary: {
                    maxlength: 2000
                },
                'evidence_urls[]': {
                    validUrl: true
                }
            },
            messages: {
                title: {
                    required: "Grant Title is required.",
                    minlength: "Title must be at least 3 characters long.",
                    maxlength: "Title cannot exceed 500 characters.",
                    titlePattern: "Title contains invalid characters. Only letters, numbers, spaces, and basic punctuation are allowed."
                },
                grant_type: {
                    required: "Grant Type is required.",
                    grantType: "Please select a valid grant type."
                },
                role: {
                    required: "Your Role is required.",
                    role: "Please select a valid role."
                },
                sponsor_name: {
                    maxlength: "Sponsor name cannot exceed 255 characters.",
                    namePattern: "Sponsor name contains invalid characters."
                },
                amount_omr: {
                    positiveNumber: "Amount must be a valid positive number.",
                    min: "Amount cannot be negative."
                },
                award_year: {
                    required: "Award Year is required.",
                    yearRange: "Please enter a valid year between 1900 and current year.",
                    digits: "Year must be a valid number."
                },
                application_date: {
                    dateRange: "Please enter a valid date.",
                    date: "Please enter a valid date format (YYYY-MM-DD)."
                },
                start_date: {
                    dateRange: "Please enter a valid date.",
                    date: "Please enter a valid date format (YYYY-MM-DD)."
                },
                end_date: {
                    dateRange: "Please enter a valid date.",
                    date: "Please enter a valid date format (YYYY-MM-DD)."
                },
                amount_received_omr: {
                    positiveNumber: "Amount received must be a valid positive number.",
                    min: "Amount received cannot be negative."
                },
                'sdgs[]': {
                    sdgFormat: "SDG format should be 'SDG 1' to 'SDG 17'."
                },
                matching_grant_moa: {
                    maxlength: "MoA reference cannot exceed 255 characters."
                },
                patent_registration_number: {
                    maxlength: "Patent registration number cannot exceed 255 characters.",
                    patentPattern: "Patent registration number should contain only uppercase letters, numbers, and hyphens."
                },
                reference_code: {
                    maxlength: "Reference code cannot exceed 100 characters.",
                    referencePattern: "Reference code should contain only uppercase letters, numbers, hyphens, and underscores."
                },
                summary: {
                    maxlength: "Summary cannot exceed 2000 characters."
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

        // Validate end date is after start date
        $('#start_date, #end_date').on('change', function() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                $('#end_date').valid();
            }
        });
    }
</script>
@endsection
