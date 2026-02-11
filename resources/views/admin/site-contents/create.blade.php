@extends('layouts.admin')

@section('page-title', 'Create Site Content')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Site Content</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.site-contents.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="key">Key <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('key') is-invalid @enderror" 
                       id="key" name="key" value="{{ old('key') }}" required placeholder="e.g., footer_description, footer_address">
                @error('key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Unique identifier for this content (use lowercase with underscores)</small>
            </div>

            <div class="form-group">
                <label for="label">Label</label>
                <input type="text" class="form-control @error('label') is-invalid @enderror" 
                       id="label" name="label" value="{{ old('label') }}" placeholder="e.g., Footer Description">
                @error('label')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Human-readable label for admin reference</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="section">Section <span class="text-danger">*</span></label>
                        <select class="form-control @error('section') is-invalid @enderror" 
                                id="section" name="section" required>
                            <option value="footer" {{ old('section') == 'footer' ? 'selected' : '' }}>Footer</option>
                            <option value="header" {{ old('section') == 'header' ? 'selected' : '' }}>Header</option>
                            <option value="general" {{ old('section') == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                        @error('section')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('type') is-invalid @enderror" 
                                id="type" name="type" required>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="html" {{ old('type') == 'html' ? 'selected' : '' }}>HTML</option>
                            <option value="url" {{ old('type') == 'url' ? 'selected' : '' }}>URL</option>
                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('type') == 'phone' ? 'selected' : '' }}>Phone</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="value">Value</label>
                <textarea class="form-control @error('value') is-invalid @enderror" 
                          id="value" name="value" rows="5">{{ old('value') }}</textarea>
                @error('value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Content value (can be HTML if type is HTML)</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="order">Order</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" 
                               id="order" name="order" value="{{ old('order', 0) }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Lower numbers appear first</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="is_active" 
                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Content
                </button>
                <a href="{{ route('admin.site-contents.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
