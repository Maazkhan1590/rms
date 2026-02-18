@extends('layouts.public')

@section('title', 'Submit Consultancy/KT | Academic Research Portal')

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
                <h1>Submit Consultancy/KT</h1>
                <p>Fill in the details below to submit your consultancy/knowledge transfer for approval</p>
            </div>
            <form method="POST" action="{{ route('consultancies.store') }}" class="auth-form" enctype="multipart/form-data">
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
                    <label for="project_consultancy_name">
                        Project/Consultancy Name <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="project_consultancy_name" name="project_consultancy_name" required 
                           value="{{ old('project_consultancy_name') }}">
                    @error('project_consultancy_name')
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
                    <label for="client_sponsor">Client/Sponsor</label>
                    <input type="text" class="form-control" id="client_sponsor" name="client_sponsor"
                           value="{{ old('client_sponsor') }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount_omr">Amount (OMR)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="amount_omr" name="amount_omr" 
                                   value="{{ old('amount_omr') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="income_type">Income Type</label>
                            <select class="form-control" id="income_type" name="income_type">
                                <option value="">Select Type</option>
                                <option value="consultancy" {{ old('income_type') == 'consultancy' ? 'selected' : '' }}>Consultancy</option>
                                <option value="service" {{ old('income_type') == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="product" {{ old('income_type') == 'product' ? 'selected' : '' }}>Product</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="commercialized" name="commercialized" value="1" {{ old('commercialized') ? 'checked' : '' }}>
                        <label class="form-check-label" for="commercialized">
                            Commercialized
                        </label>
                    </div>
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

                <div class="form-group">
                    <label>Evidence Files</label>
                    <input type="file" class="form-control-file" name="evidence_files[]" multiple accept=".pdf,.doc,.docx,.zip">
                </div>

                <div class="form-group">
                    <label>Additional Evidence URLs</label>
                    <div id="evidence-urls-container">
                        <input type="url" class="form-control mb-2" name="evidence_urls[]" placeholder="https://example.com/evidence">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-evidence-url">
                        <i class="fa fa-plus"></i> Add Another URL
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sdg_s">SDG Numbers</label>
                            <input type="text" class="form-control" id="sdg_s" name="sdg_s"
                                   placeholder="e.g., 1,3,4" value="{{ old('sdg_s') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="year">Year</label>
                            <input type="number" class="form-control" id="year" name="year" 
                                   min="1900" max="{{ date('Y') }}" value="{{ old('year', date('Y')) }}">
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
@endsection
