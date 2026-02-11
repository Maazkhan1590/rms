@extends('layouts.admin')

@section('page-title', 'Create Slider')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Slider</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title') }}">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tag">Tag</label>
                <input type="text" class="form-control @error('tag') is-invalid @enderror" 
                       id="tag" name="tag" value="{{ old('tag') }}" placeholder="e.g., New Research, Open Access">
                @error('tag')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Small tag displayed above the title</small>
            </div>

            <div class="form-group">
                <label for="image">Upload Image</label>
                <input type="file" class="form-control-file @error('image') is-invalid @enderror" 
                       id="image" name="image" accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Or use external URL below</small>
            </div>

            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                       id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
                @error('image_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">External image URL (if not uploading)</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="button_text">Primary Button Text</label>
                        <input type="text" class="form-control @error('button_text') is-invalid @enderror" 
                               id="button_text" name="button_text" value="{{ old('button_text') }}">
                        @error('button_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="button_link">Primary Button Link</label>
                        <input type="text" class="form-control @error('button_link') is-invalid @enderror" 
                               id="button_link" name="button_link" value="{{ old('button_link') }}">
                        @error('button_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="button_text_secondary">Secondary Button Text</label>
                        <input type="text" class="form-control @error('button_text_secondary') is-invalid @enderror" 
                               id="button_text_secondary" name="button_text_secondary" value="{{ old('button_text_secondary') }}">
                        @error('button_text_secondary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="button_link_secondary">Secondary Button Link</label>
                        <input type="text" class="form-control @error('button_link_secondary') is-invalid @enderror" 
                               id="button_link_secondary" name="button_link_secondary" value="{{ old('button_link_secondary') }}">
                        @error('button_link_secondary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
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
                    <span class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">save</span> Create Slider
                </button>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
