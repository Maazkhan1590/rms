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
                           placeholder="Enter partner organization name"
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
document.addEventListener('DOMContentLoaded', function() {
    // Add evidence URL field
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

    // Remove evidence URL field
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-url')) {
            e.target.closest('.evidence-url-item').remove();
        }
    });
});
</script>
@endsection
