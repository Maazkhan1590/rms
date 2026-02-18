@extends('layouts.public')

@section('title', 'Submit Award | Academic Research Portal')

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
                <h1>Submit Award</h1>
                <p>Fill in the details below to submit your award for approval</p>
            </div>
            <form method="POST" action="{{ route('awards.store') }}" class="auth-form" enctype="multipart/form-data">
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
                    <label for="award_name">
                        Award Name <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="text" class="form-control" id="award_name" name="award_name" required 
                           value="{{ old('award_name') }}">
                    @error('award_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="awarding_organization">Awarding Organization</label>
                            <input type="text" class="form-control" id="awarding_organization" name="awarding_organization"
                                   value="{{ old('awarding_organization') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="award_date">Award Date</label>
                            <input type="date" class="form-control" id="award_date" name="award_date" 
                                   value="{{ old('award_date') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="award_type">Award Type</label>
                            <select class="form-control" id="award_type" name="award_type">
                                <option value="">Select Type</option>
                                <option value="national" {{ old('award_type') == 'national' ? 'selected' : '' }}>National</option>
                                <option value="international" {{ old('award_type') == 'international' ? 'selected' : '' }}>International</option>
                                <option value="regional" {{ old('award_type') == 'regional' ? 'selected' : '' }}>Regional</option>
                                <option value="institutional" {{ old('award_type') == 'institutional' ? 'selected' : '' }}>Institutional</option>
                                <option value="other" {{ old('award_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category"
                                   value="{{ old('category') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="achievement_description">Achievement Description</label>
                    <textarea class="form-control" id="achievement_description" name="achievement_description" rows="4">{{ old('achievement_description') }}</textarea>
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

                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" class="form-control" id="year" name="year" 
                           min="1900" max="{{ date('Y') }}" value="{{ old('year', date('Y')) }}">
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
