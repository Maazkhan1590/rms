@extends('layouts.admin')

@section('page-title', 'Edit Site Content')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Site Content</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.site-contents.update', $siteContent) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="key">Key <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('key') is-invalid @enderror" 
                       id="key" name="key" value="{{ old('key', $siteContent->key) }}" required>
                @error('key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="label">Label</label>
                <input type="text" class="form-control @error('label') is-invalid @enderror" 
                       id="label" name="label" value="{{ old('label', $siteContent->label) }}">
                @error('label')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="section">Section <span class="text-danger">*</span></label>
                        <select class="form-control @error('section') is-invalid @enderror" 
                                id="section" name="section" required>
                            <option value="footer" {{ old('section', $siteContent->section) == 'footer' ? 'selected' : '' }}>Footer</option>
                            <option value="header" {{ old('section', $siteContent->section) == 'header' ? 'selected' : '' }}>Header</option>
                            <option value="general" {{ old('section', $siteContent->section) == 'general' ? 'selected' : '' }}>General</option>
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
                            <option value="text" {{ old('type', $siteContent->type) == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="html" {{ old('type', $siteContent->type) == 'html' ? 'selected' : '' }}>HTML</option>
                            <option value="url" {{ old('type', $siteContent->type) == 'url' ? 'selected' : '' }}>URL</option>
                            <option value="email" {{ old('type', $siteContent->type) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('type', $siteContent->type) == 'phone' ? 'selected' : '' }}>Phone</option>
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
                          id="value" name="value" rows="5">{{ old('value', $siteContent->value) }}</textarea>
                @error('value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="order">Order</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" 
                               id="order" name="order" value="{{ old('order', $siteContent->order) }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="is_active" 
                                   name="is_active" value="1" {{ old('is_active', $siteContent->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <span class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">save</span> Update Content
                </button>
                <a href="{{ route('admin.site-contents.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
