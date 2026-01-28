@extends('layouts.public')

@section('title', 'Submit Publication | Academic Research Portal')

@section('content')
<style>
    .auth-container {
        max-width: 900px !important;
    }

    /* Stepper Styles */
    .stepper-container {
        margin-bottom: 3rem;
        position: relative;
    }

    .stepper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        padding: 0 2rem;
    }

    .stepper::before {
        content: '';
        position: absolute;
        top: 30px;
        left: 10%;
        right: 10%;
        height: 3px;
        background: var(--border-color);
        z-index: 1;
        border-radius: 2px;
    }

    .stepper-line {
        position: absolute;
        top: 30px;
        left: 10%;
        height: 3px;
        background: var(--primary-color);
        z-index: 2;
        border-radius: 2px;
        transition: width 0.3s ease;
        width: 0%;
    }

    .step-item {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 3;
    }

    .step-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--light-color);
        color: var(--text-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-weight: 700;
        font-size: 1.125rem;
        border: 3px solid var(--border-color);
        transition: all 0.3s ease;
        position: relative;
    }

    .step-circle.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        transform: scale(1.1);
    }

    .step-circle.completed {
        background: var(--success);
        color: white;
        border-color: var(--success);
    }

    .step-circle.completed::after {
        content: '✓';
        position: absolute;
        font-size: 1.5rem;
    }

    .step-label {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-primary);
        transition: color 0.3s ease;
    }

    .step-item.active .step-label {
        color: var(--primary-color);
    }

    .step-item.completed .step-label {
        color: var(--success);
    }

    /* Form Styles */
    .step-content {
        animation: fadeInUp 0.4s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .step-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 2rem;
        color: var(--text-color);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
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

    .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    /* Submission Type Selection Styles */
    .submission-type-selection {
        background: var(--light-color);
        padding: 2rem;
        border-radius: 16px;
        border: 2px solid var(--border-color);
    }

    .submission-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .submission-type-btn {
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .submission-type-btn:hover {
        border-color: var(--primary-color);
        background: rgba(100, 255, 218, 0.05);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .submission-type-btn i {
        font-size: 2rem;
        color: var(--primary-color);
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

    /* Author Item Styles */
    .author-item {
        margin-bottom: 1.5rem;
        padding: 2rem;
        background: var(--light-color);
        border-radius: 16px;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .author-item:hover {
        border-color: var(--accent-color);
        box-shadow: var(--shadow-md);
    }

    .author-item.primary {
        background: linear-gradient(135deg, rgba(100, 255, 218, 0.1) 0%, rgba(100, 255, 218, 0.05) 100%);
        border-color: var(--accent-color);
    }

    .author-header {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1.5rem;
        align-items: start;
    }

    .author-controls {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: flex-end;
    }

    .primary-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: background 0.2s ease;
    }

    .checkbox-wrapper:hover {
        background: rgba(100, 255, 218, 0.1);
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--accent-color);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-card {
            padding: 2rem 1.5rem;
        }

        .stepper {
            padding: 0 1rem;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            font-size: 1rem;
        }

        .author-header {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Submit Paper</h1>
                <p>Share your research work with the academic community</p>
            </div>
            
            <!-- Submission Type Selection -->
            <div id="submissionTypeSelection" class="submission-type-selection" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--text-color);">What would you like to submit?</h3>
                <div class="submission-type-grid">
                    <button type="button" class="submission-type-btn" onclick="selectSubmissionType('publication')">
                        <i class="fas fa-book"></i>
                        <span>Publication</span>
                    </button>
                    <button type="button" class="submission-type-btn" onclick="selectSubmissionType('grant')">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Grant</span>
                    </button>
                    <button type="button" class="submission-type-btn" onclick="selectSubmissionType('rtn')">
                        <i class="fas fa-graduation-cap"></i>
                        <span>RTN</span>
                    </button>
                    <button type="button" class="submission-type-btn" onclick="selectSubmissionType('bonus')">
                        <i class="fas fa-award"></i>
                        <span>Bonus Recognition</span>
                    </button>
                </div>
            </div>
            
            <form id="publicationForm" method="POST" action="{{ route('publications.store') }}" class="auth-form" enctype="multipart/form-data" style="display: none;">
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

                <!-- Stepper Progress -->
                <div class="stepper-container">
                    <div class="stepper">
                        <div class="stepper-line" id="stepperLine"></div>
                        <div class="step-item active" data-step="1">
                            <div class="step-circle active">1</div>
                            <div class="step-label">Basic Info</div>
                        </div>
                        <div class="step-item" data-step="2">
                            <div class="step-circle">2</div>
                            <div class="step-label">Details</div>
                        </div>
                        <div class="step-item" data-step="3">
                            <div class="step-circle">3</div>
                            <div class="step-label">Authors</div>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Basic Information -->
                <div class="step-content" data-step="1">
                    <h2 class="step-title">Basic Information</h2>
                    
                    <div class="form-group">
                        <label for="title">
                            Publication Title <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               placeholder="Enter the full title of your publication"
                               value="{{ old('title') }}">
                        @error('title')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="publication_type">
                            Publication Type <span style="color: var(--danger);">*</span>
                        </label>
                        <select class="form-control" id="publication_type" name="publication_type" required>
                            <option value="">Select Publication Type</option>
                            <option value="journal" {{ old('publication_type') == 'journal' ? 'selected' : '' }}>Journal Article</option>
                            <option value="conference" {{ old('publication_type') == 'conference' ? 'selected' : '' }}>Conference Paper</option>
                            <option value="book" {{ old('publication_type') == 'book' ? 'selected' : '' }}>Book</option>
                            <option value="book_chapter" {{ old('publication_type') == 'book_chapter' ? 'selected' : '' }}>Book Chapter</option>
                            <option value="patent" {{ old('publication_type') == 'patent' ? 'selected' : '' }}>Patent</option>
                            <option value="other" {{ old('publication_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('publication_type')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="publication_year">
                            Publication Year <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="number" class="form-control" id="publication_year" name="publication_year" 
                               required min="1900" max="{{ date('Y') }}" 
                               value="{{ old('publication_year', date('Y')) }}">
                        @error('publication_year')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="abstract">Abstract</label>
                        <textarea class="form-control" id="abstract" name="abstract" rows="6" 
                                  placeholder="Provide a brief summary of your publication...">{{ old('abstract') }}</textarea>
                        @error('abstract')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <div></div>
                        <button type="button" class="btn btn-primary btn-block" onclick="nextStep()">
                            Next Step <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Publication Details -->
                <div class="step-content" data-step="2" style="display: none;">
                    <h2 class="step-title">Publication Details</h2>
                    
                    <div id="journal_fields" style="display: none;">
                        <div class="form-group">
                            <label for="journal_name">Journal Name</label>
                            <input type="text" class="form-control" id="journal_name" name="journal_name" 
                                   placeholder="Enter journal name"
                                   value="{{ old('journal_name') }}">
                            @error('journal_name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="conference_fields" style="display: none;">
                        <div class="form-group">
                            <label for="conference_name">Conference Name</label>
                            <input type="text" class="form-control" id="conference_name" name="conference_name" 
                                   placeholder="Enter conference name"
                                   value="{{ old('conference_name') }}">
                            @error('conference_name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="book_fields" style="display: none;">
                        <div class="form-group">
                            <label for="publisher">Publisher</label>
                            <input type="text" class="form-control" id="publisher" name="publisher" 
                                   placeholder="Enter publisher name"
                                   value="{{ old('publisher') }}">
                            @error('publisher')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="doi">DOI (Digital Object Identifier)</label>
                        <input type="text" class="form-control" id="doi" name="doi" 
                               placeholder="10.xxxx/xxxxx"
                               value="{{ old('doi') }}">
                        @error('doi')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" 
                               placeholder="Enter ISBN"
                               value="{{ old('isbn') }}">
                        @error('isbn')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="published_link">Publication Link</label>
                        <input type="url" class="form-control" id="published_link" name="published_link" 
                               placeholder="https://..."
                               value="{{ old('published_link') }}">
                        @error('published_link')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="proceedings_link">Proceedings Link</label>
                        <input type="url" class="form-control" id="proceedings_link" name="proceedings_link" 
                               placeholder="https://..."
                               value="{{ old('proceedings_link') }}">
                        @error('proceedings_link')
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

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary btn-block" onclick="nextStep()">
                            Next Step <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Authors -->
                <div class="step-content" data-step="3" style="display: none;">
                    <h2 class="step-title">Authors</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                        Add all authors for this publication. Mark one as the primary author.
                    </p>
                    
                    <div id="authors-container">
                        <div class="author-item primary" data-author-index="0">
                            <div class="author-header">
                                <div style="flex: 1;">
                                    <div class="form-group">
                                        <label>
                                            Author Name <span style="color: var(--danger);">*</span>
                                        </label>
                                        <input type="text" class="form-control author-name" 
                                               name="authors[0][name]" required 
                                               placeholder="Enter author full name">
                                    </div>
                                    <div class="form-group">
                                        <label>Email (Optional)</label>
                                        <input type="email" class="form-control author-email" 
                                               name="authors[0][email]" 
                                               placeholder="author@example.com">
                                    </div>
                                </div>
                                <div class="author-controls">
                                    <div class="primary-badge">
                                        <i class="fas fa-star"></i> Primary Author
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="removeAuthor(this)" style="display: none;">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="authors[0][is_primary]" value="1">
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline" onclick="addAuthor()" style="margin-bottom: 2rem;">
                        <i class="fas fa-plus"></i> Add Another Author
                    </button>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> Submit Paper
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    let currentStep = 1;
    let authorCount = 1;

    function updateStepDisplay() {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(step => {
            step.style.display = 'none';
        });

        // Show current step
        const currentStepContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
        if (currentStepContent) {
            currentStepContent.style.display = 'block';
        }

        // Update step items
        document.querySelectorAll('.step-item').forEach((item, index) => {
            const stepNum = index + 1;
            const circle = item.querySelector('.step-circle');
            
            item.classList.remove('active', 'completed');
            circle.classList.remove('active', 'completed');
            
            if (stepNum < currentStep) {
                item.classList.add('completed');
                circle.classList.add('completed');
            } else if (stepNum === currentStep) {
                item.classList.add('active');
                circle.classList.add('active');
            }
        });

        // Update stepper line
        const stepperLine = document.getElementById('stepperLine');
        if (currentStep > 1) {
            const progress = ((currentStep - 1) / 2) * 80; // 80% is the line width
            stepperLine.style.width = progress + '%';
        } else {
            stepperLine.style.width = '0%';
        }
    }

    function nextStep() {
        // Validate current step
        const currentStepContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
        if (!currentStepContent) return;

        const requiredFields = currentStepContent.querySelectorAll('[required]');
        let isValid = true;
        let firstInvalid = null;

        requiredFields.forEach(field => {
            field.classList.remove('is-invalid');
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                if (!firstInvalid) firstInvalid = field;
            }
        });

        if (!isValid) {
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
            return;
        }

        if (currentStep < 3) {
            currentStep++;
            updateStepDisplay();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function addAuthor() {
        const container = document.getElementById('authors-container');
        const newAuthor = document.createElement('div');
        newAuthor.className = 'author-item';
        newAuthor.setAttribute('data-author-index', authorCount);
        newAuthor.innerHTML = `
            <div class="author-header">
                <div style="flex: 1;">
                    <div class="form-group">
                        <label class="form-label">
                            Author Name <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" class="form-control author-name" 
                               name="authors[${authorCount}][name]" required 
                               placeholder="Enter author full name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email (Optional)</label>
                        <input type="email" class="form-control author-email" 
                               name="authors[${authorCount}][email]" 
                               placeholder="author@example.com">
                    </div>
                </div>
                <div class="author-controls">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="authors[${authorCount}][is_primary]" 
                               value="1" onchange="updatePrimaryAuthor(this)">
                        <span style="font-size: 0.875rem; font-weight: 500;">Primary</span>
                    </label>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeAuthor(this)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newAuthor);
        authorCount++;
        updateRemoveButtons();
    }

    function removeAuthor(button) {
        const authorItem = button.closest('.author-item');
        const isPrimary = authorItem.classList.contains('primary');
        
        if (isPrimary && document.querySelectorAll('.author-item').length > 1) {
            // Make the first remaining author primary
            const remainingAuthors = document.querySelectorAll('.author-item');
            if (remainingAuthors.length > 1) {
                const firstAuthor = remainingAuthors[0] === authorItem ? remainingAuthors[1] : remainingAuthors[0];
                makePrimary(firstAuthor);
            }
        }
        
        authorItem.remove();
        updateRemoveButtons();
    }

    function updatePrimaryAuthor(checkbox) {
        if (checkbox.checked) {
            // Uncheck all other primary checkboxes
            document.querySelectorAll('input[name*="[is_primary]"]').forEach(cb => {
                if (cb !== checkbox) cb.checked = false;
            });
            
            // Remove primary class from all author items
            document.querySelectorAll('.author-item').forEach(item => {
                item.classList.remove('primary');
                const badge = item.querySelector('.primary-badge');
                if (badge) badge.remove();
            });
            
            // Add primary class to current author item
            const authorItem = checkbox.closest('.author-item');
            makePrimary(authorItem);
        }
    }

    function makePrimary(authorItem) {
        authorItem.classList.add('primary');
        const controls = authorItem.querySelector('.author-controls');
        const existingBadge = authorItem.querySelector('.primary-badge');
        if (!existingBadge && controls) {
            const badge = document.createElement('div');
            badge.className = 'primary-badge';
            badge.innerHTML = '<i class="fas fa-star"></i> Primary Author';
            controls.insertBefore(badge, controls.firstChild);
        }
        
        // Set hidden input for primary
        const hiddenInput = authorItem.querySelector('input[type="hidden"][name*="[is_primary]"]');
        if (!hiddenInput) {
            const input = document.createElement('input');
            input.type = 'hidden';
            const nameInput = authorItem.querySelector('input.author-name');
            if (nameInput) {
                const name = nameInput.name;
                input.name = name.replace('[name]', '[is_primary]');
                input.value = '1';
                authorItem.appendChild(input);
            }
        }
    }

    function updateRemoveButtons() {
        const authorItems = document.querySelectorAll('.author-item');
        authorItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.btn-danger');
            if (removeBtn) {
                if (authorItems.length > 1) {
                    removeBtn.style.display = 'block';
                } else {
                    removeBtn.style.display = 'none';
                }
            }
        });
    }

    // Show/hide fields based on publication type
    document.getElementById('publication_type')?.addEventListener('change', function() {
        const type = this.value;
        document.getElementById('journal_fields').style.display = type === 'journal' ? 'block' : 'none';
        document.getElementById('conference_fields').style.display = type === 'conference' ? 'block' : 'none';
        document.getElementById('book_fields').style.display = (type === 'book' || type === 'book_chapter') ? 'block' : 'none';
    });

    // Initialize
    updateStepDisplay();
    updateRemoveButtons();

    // Trigger publication type change if value exists
    const publicationType = document.getElementById('publication_type');
    if (publicationType && publicationType.value) {
        publicationType.dispatchEvent(new Event('change'));
    }

    // Submission Type Selection
    function selectSubmissionType(type) {
        const form = document.getElementById('publicationForm');
        const selection = document.getElementById('submissionTypeSelection');
        
        if (type === 'publication') {
            // Show publication form
            form.style.display = 'block';
            selection.style.display = 'none';
            form.action = "{{ route('publications.store') }}";
        } else if (type === 'grant') {
            // Redirect to grant form
            window.location.href = "{{ route('grants.create') }}";
        } else if (type === 'rtn') {
            // Redirect to RTN form
            window.location.href = "{{ route('rtn-submissions.create') }}";
        } else if (type === 'bonus') {
            // Redirect to bonus recognition form
            window.location.href = "{{ route('bonus-recognitions.create') }}";
        }
    }

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
