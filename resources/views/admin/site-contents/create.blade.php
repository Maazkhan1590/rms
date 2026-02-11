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
                
                <!-- Toggle between predefined and custom -->
                <div class="mb-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btn-predefined" onclick="toggleKeyMode('predefined')">
                            <span class="material-icons-outlined" style="font-size: 16px;">list</span> Predefined Key
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btn-custom" onclick="toggleKeyMode('custom')">
                            <span class="material-icons-outlined" style="font-size: 16px;">edit</span> Custom Key
                        </button>
                    </div>
                </div>

                <!-- Predefined Key Dropdown -->
                <div id="predefined-key-section">
                    <select class="form-control @error('key') is-invalid @enderror" 
                            id="key_select" onchange="document.getElementById('key').value = this.value; updateLabelFromKey();">
                        <option value="">-- Select Predefined Key --</option>
                        @foreach($predefinedKeys as $section => $keys)
                            <optgroup label="{{ ucfirst($section) }} Section">
                                @foreach($keys as $keyValue => $keyLabel)
                                    <option value="{{ $keyValue }}" data-label="{{ $keyLabel }}" data-section="{{ $section }}" data-type="{{ 
                                        strpos($keyValue, 'email') !== false ? 'email' : 
                                        (strpos($keyValue, 'phone') !== false ? 'phone' : 
                                        (strpos($keyValue, 'link') !== false || strpos($keyValue, 'social_') !== false ? 'url' : 
                                        (strpos($keyValue, 'description') !== false ? 'html' : 'text'))) 
                                    }}">
                                        {{ $keyValue }} ({{ $keyLabel }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <small class="form-text text-muted mt-1">Select from commonly used keys for footer, header, or general content</small>
                </div>

                <!-- Custom Key Input -->
                <div id="custom-key-section" style="display: none;">
                    <input type="text" class="form-control @error('key') is-invalid @enderror" 
                           id="key" name="key" value="{{ old('key') }}" required 
                           placeholder="e.g., custom_feature_text, my_custom_setting">
                    <small class="form-text text-muted mt-1">
                        <strong>Custom Key Guidelines:</strong><br>
                        • Use lowercase letters only<br>
                        • Separate words with underscores (e.g., <code>custom_feature_name</code>)<br>
                        • Must be unique across all site content<br>
                        • Examples: <code>homepage_banner_text</code>, <code>about_us_description</code>, <code>custom_widget_content</code>
                    </small>
                </div>

                <!-- Hidden input that actually submits -->
                <input type="hidden" id="key_hidden" name="key" value="{{ old('key') }}" required>

                @error('key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                    <span class="material-icons-outlined">save</span> Create Content
                </button>
                <a href="{{ route('admin.site-contents.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleKeyMode(mode) {
        const predefinedSection = document.getElementById('predefined-key-section');
        const customSection = document.getElementById('custom-key-section');
        const btnPredefined = document.getElementById('btn-predefined');
        const btnCustom = document.getElementById('btn-custom');
        const keySelect = document.getElementById('key_select');
        const keyInput = document.getElementById('key');
        const keyHidden = document.getElementById('key_hidden');

        if (mode === 'predefined') {
            predefinedSection.style.display = 'block';
            customSection.style.display = 'none';
            btnPredefined.classList.add('active');
            btnPredefined.classList.remove('btn-outline-primary');
            btnPredefined.classList.add('btn-primary');
            btnCustom.classList.remove('active', 'btn-primary');
            btnCustom.classList.add('btn-outline-secondary');
            keySelect.required = true;
            keyInput.required = false;
            keyInput.name = '';
            keySelect.name = 'key';
            keySelect.onchange = function() {
                keyHidden.value = this.value;
                updateLabelFromKey();
            };
        } else {
            predefinedSection.style.display = 'none';
            customSection.style.display = 'block';
            btnCustom.classList.add('active');
            btnCustom.classList.remove('btn-outline-secondary');
            btnCustom.classList.add('btn-primary');
            btnPredefined.classList.remove('active', 'btn-primary');
            btnPredefined.classList.add('btn-outline-primary');
            keySelect.required = false;
            keyInput.required = true;
            keySelect.name = '';
            keyInput.name = 'key';
            keyInput.oninput = function() {
                keyHidden.value = this.value;
            };
            keyInput.focus();
        }
    }

    function updateLabelFromKey() {
        const select = document.getElementById('key_select');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            // Update hidden input
            document.getElementById('key_hidden').value = selectedOption.value;
            
            // Update label field
            const labelInput = document.getElementById('label');
            if (labelInput && !labelInput.value) {
                labelInput.value = selectedOption.getAttribute('data-label') || '';
            }
            
            // Update section field
            const sectionSelect = document.getElementById('section');
            if (sectionSelect && selectedOption.getAttribute('data-section')) {
                sectionSelect.value = selectedOption.getAttribute('data-section');
            }
            
            // Update type field
            const typeSelect = document.getElementById('type');
            if (typeSelect && selectedOption.getAttribute('data-type')) {
                typeSelect.value = selectedOption.getAttribute('data-type');
            }
        }
    }
    
    // Initialize - check if old value exists and set mode accordingly
    document.addEventListener('DOMContentLoaded', function() {
        const oldKey = '{{ old("key") }}';
        if (oldKey) {
            const keySelect = document.getElementById('key_select');
            let found = false;
            for (let i = 0; i < keySelect.options.length; i++) {
                if (keySelect.options[i].value === oldKey) {
                    keySelect.value = oldKey;
                    found = true;
                    toggleKeyMode('predefined');
                    updateLabelFromKey();
                    break;
                }
            }
            if (!found) {
                document.getElementById('key').value = oldKey;
                document.getElementById('key_hidden').value = oldKey;
                toggleKeyMode('custom');
            }
        } else {
            // Default to predefined mode
            toggleKeyMode('predefined');
        }
    });
</script>
@endpush
@endsection
