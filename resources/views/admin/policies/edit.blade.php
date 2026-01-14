@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Edit Scoring Policy</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.policies.update', $policy->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Policy Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $policy->name) }}" required>
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
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type', $policy->type) == $type ? 'selected' : '' }}>
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
                               id="category" name="category" value="{{ old('category', $policy->category) }}">
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="subcategory">Subcategory</label>
                        <input type="text" class="form-control @error('subcategory') is-invalid @enderror" 
                               id="subcategory" name="subcategory" value="{{ old('subcategory', $policy->subcategory) }}">
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
                               id="points" name="points" value="{{ old('points', $policy->points) }}" required>
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
                               id="cap" name="cap" value="{{ old('cap', $policy->cap) }}">
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
                                <option value="{{ $id }}" {{ old('policy_version_id', $policy->policy_version_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
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
                               id="version" name="version" value="{{ old('version', $policy->version) }}">
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
                               value="{{ old('effective_from', $policy->effective_from->format('Y-m-d')) }}" required>
                        @error('effective_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="effective_to">Effective To</label>
                        <input type="date" class="form-control @error('effective_to') is-invalid @enderror" 
                               id="effective_to" name="effective_to" 
                               value="{{ old('effective_to', $policy->effective_to ? $policy->effective_to->format('Y-m-d') : '') }}">
                        @error('effective_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $policy->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (Policy is currently in effect)
                    </label>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Policy
                </button>
                <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
