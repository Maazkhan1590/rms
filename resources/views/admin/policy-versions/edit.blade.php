@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Edit Policy Version</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.policy-versions.update', $policyVersion->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="version_number">Version Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('version_number') is-invalid @enderror" 
                               id="version_number" name="version_number" 
                               value="{{ old('version_number', $policyVersion->version_number) }}" required>
                        @error('version_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="year">Year <span class="text-danger">*</span></label>
                        <input type="number" min="2000" max="{{ now()->year + 10 }}" 
                               class="form-control @error('year') is-invalid @enderror" 
                               id="year" name="year" value="{{ old('year', $policyVersion->year) }}" required>
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description', $policyVersion->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $policyVersion->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Set as Active Version (will deactivate other versions)
                    </label>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Version
                </button>
                <a href="{{ route('admin.policy-versions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
