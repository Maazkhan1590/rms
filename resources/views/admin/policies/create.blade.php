@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-plus"></i> Create Scoring Policy</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.policies.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Policy Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        <small class="form-text text-muted">e.g., "Journal Paper (Scopus Q1)", "External Grant (PI)"</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('type') is-invalid @enderror" 
                                id="type" name="type" required>
                            <option value="">Select Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" 
                               id="category" name="category" value="{{ old('category') }}"
                               placeholder="e.g., Journal, Conference, Book, External Grant">
                        <small class="form-text text-muted">Main category classification</small>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="subcategory">Subcategory</label>
                        <input type="text" class="form-control @error('subcategory') is-invalid @enderror" 
                               id="subcategory" name="subcategory" value="{{ old('subcategory') }}"
                               placeholder="e.g., Q1, Q2, Scopus, Non-indexed, PI, Co-PI">
                        <small class="form-text text-muted">Sub-classification (e.g., Quartile, Role)</small>
                        @error('subcategory')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="points">Points <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" 
                               class="form-control @error('points') is-invalid @enderror" 
                               id="points" name="points" value="{{ old('points') }}" required>
                        <small class="form-text text-muted">Points allocated per item</small>
                        @error('points')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cap">Cap (Maximum Points)</label>
                        <input type="number" step="0.01" min="0" 
                               class="form-control @error('cap') is-invalid @enderror" 
                               id="cap" name="cap" value="{{ old('cap') }}">
                        <small class="form-text text-muted">Maximum total points (e.g., 120 for journals, 15 for conferences)</small>
                        @error('cap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="policy_version_id">Policy Version <span class="text-danger">*</span></label>
                        <select class="form-control @error('policy_version_id') is-invalid @enderror" 
                                id="policy_version_id" name="policy_version_id" required>
                            <option value="">Select Policy Version</option>
                            @foreach($versions as $id => $name)
                                <option value="{{ $id }}" {{ old('policy_version_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            @if($versions->isEmpty())
                                <a href="{{ route('admin.policy-versions.create') }}" class="text-danger">No policy versions found. Create one first.</a>
                            @else
                                Select the policy version this policy belongs to
                            @endif
                        </small>
                        @error('policy_version_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="version">Version Identifier (Optional)</label>
                        <input type="text" class="form-control @error('version') is-invalid @enderror" 
                               id="version" name="version" value="{{ old('version', $currentYear) }}">
                        <small class="form-text text-muted">Additional version identifier (e.g., "v1.0", "2024.1")</small>
                        @error('version')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="effective_from">Effective From <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                               id="effective_from" name="effective_from" 
                               value="{{ old('effective_from', now()->format('Y-m-d')) }}" required>
                        @error('effective_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="effective_to">Effective To</label>
                        <input type="date" class="form-control @error('effective_to') is-invalid @enderror" 
                               id="effective_to" name="effective_to" value="{{ old('effective_to') }}">
                        <small class="form-text text-muted">Leave empty for ongoing policy</small>
                        @error('effective_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (Policy is currently in effect)
                    </label>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Create Policy
                </button>
                <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Quick Reference Guide -->
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-book"></i> Quick Reference: Standard Scoring Values</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Publications</h6>
                <ul>
                    <li><strong>Journal (Indexed):</strong> 60 points (Cap: 120)</li>
                    <li><strong>Conference Paper:</strong> 15 points (Cap: 15)</li>
                    <li><strong>Book/Chapter:</strong> 10 points</li>
                    <li><strong>Non-indexed Journal:</strong> 5 points</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Grants (by Role)</h6>
                <ul>
                    <li><strong>External Grant (PI):</strong> 90 points</li>
                    <li><strong>Matching Grant (PI):</strong> 15 points</li>
                    <li><strong>GRG/URG (Advisor):</strong> 10 points</li>
                    <li><strong>Patent (SU-registered):</strong> 10 points</li>
                    <li><strong>Grant Application:</strong> 5 points</li>
                    <li><strong>Co-PI:</strong> 5 points</li>
                    <li><strong>Co-I:</strong> 6 points</li>
                </ul>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6>RTN</h6>
                <ul>
                    <li><strong>RTN-3 (Student Co-author):</strong> 5 points</li>
                    <li><strong>RTN-4 (Research in Teaching):</strong> 5 points</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Bonuses (Cap: 25 total)</h6>
                <ul>
                    <li><strong>Editorial Board:</strong> 5 points</li>
                    <li><strong>External Examiner:</strong> 6 points</li>
                    <li><strong>Regulatory/Professional Body:</strong> 7 points</li>
                    <li><strong>Workshop/Seminar:</strong> 8 points</li>
                    <li><strong>Keynote/Plenary:</strong> 9 points</li>
                    <li><strong>Journal Reviewer:</strong> 5 points</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
