@extends('layouts.public')

@section('title', 'Submit Commercialization | Academic Research Portal')

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
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1>Submit Commercialization</h1>
                <p>Fill in the details below to submit your commercialization for approval</p>
            </div>
            <form id="commercializationForm" method="POST" action="{{ route('commercializations.store') }}" class="auth-form" enctype="multipart/form-data">
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
                    <label for="product_service_name">
                        Product/Service Name <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="product_service_name" name="product_service_name" required 
                           placeholder="Enter product or service name"
                           value="{{ old('product_service_name') }}">
                    @error('product_service_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">
                                Type <span style="color: var(--danger);">*</span>
                            </label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Product</option>
                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                            </select>
                            @error('type')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stage">Stage</label>
                            <select class="form-control" id="stage" name="stage">
                                <option value="">Select Stage</option>
                                <option value="prototype" {{ old('stage') == 'prototype' ? 'selected' : '' }}>Prototype</option>
                                <option value="pilot" {{ old('stage') == 'pilot' ? 'selected' : '' }}>Pilot</option>
                                <option value="launched" {{ old('stage') == 'launched' ? 'selected' : '' }}>Launched</option>
                            </select>
                            @error('stage')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="launch_date">Launch Date</label>
                            <input type="date" class="form-control" id="launch_date" name="launch_date" 
                                   value="{{ old('launch_date') }}">
                            @error('launch_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="revenue_omr">Revenue (OMR)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="revenue_omr" name="revenue_omr" 
                                   value="{{ old('revenue_omr') }}">
                            @error('revenue_omr')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="client_market">Client/Market</label>
                    <input type="text" class="form-control" id="client_market" name="client_market"
                           placeholder="Describe target client or market"
                           value="{{ old('client_market') }}">
                    @error('client_market')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="ip_patent" name="ip_patent" value="1" {{ old('ip_patent') ? 'checked' : '' }}>
                        <label class="form-check-label" for="ip_patent">
                            IP/Patent Registered
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="evidence_link">Evidence Link</label>
                    <input type="url" class="form-control" id="evidence_link" name="evidence_link"
                           placeholder="https://example.com/evidence"
                           value="{{ old('evidence_link') }}">
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

                <div class="form-group">
                    <label>Evidence Files</label>
                    <div class="evidence-upload-area">
                        <input type="file" class="form-control-file" id="evidence_files" name="evidence_files[]" 
                               multiple accept=".pdf,.doc,.docx,.zip">
                        <small class="form-text text-muted">You can upload multiple files (PDF, Word, ZIP). Maximum 10MB per file.</small>
                        @error('evidence_files.*')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Additional Evidence URLs</label>
                    <div id="evidence-urls-container">
                        <div class="evidence-url-item mb-2">
                            <input type="url" class="form-control" name="evidence_urls[]" 
                                   placeholder="https://example.com/additional-evidence">
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-evidence-url">
                        <i class="fa fa-plus"></i> Add Another URL
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sdg_s">SDG Numbers (Comma-separated)</label>
                            <input type="text" class="form-control" id="sdg_s" name="sdg_s"
                                   placeholder="e.g., 1,3,4,7"
                                   value="{{ old('sdg_s') }}">
                            @error('sdg_s')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="year">Year</label>
                            <input type="number" class="form-control" id="year" name="year" 
                                   min="1900" max="{{ date('Y') }}" 
                                   value="{{ old('year', date('Y')) }}">
                            @error('year')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
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
        newField.className = 'evidence-url-item mb-2';
        newField.innerHTML = `
            <input type="url" class="form-control" name="evidence_urls[]" 
                   placeholder="https://example.com/additional-evidence">
            <button type="button" class="btn btn-sm btn-danger remove-url">
                <i class="fa fa-times"></i>
            </button>
        `;
        container.appendChild(newField);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-url')) {
            e.target.closest('.evidence-url-item').remove();
        }
    });
});
</script>
@endsection
