@extends('layouts.admin')

@section('page-title', 'Edit College')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit College: {{ $college->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.colleges.update', $college) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code">College Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                       id="code" name="code" value="{{ old('code', $college->code) }}" required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">College Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $college->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="dean_id">Dean</label>
                <select class="form-control @error('dean_id') is-invalid @enderror" 
                        id="dean_id" name="dean_id">
                    <option value="">Select Dean</option>
                    @foreach($deans as $id => $name)
                        <option value="{{ $id }}" {{ old('dean_id', $college->dean_id) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('dean_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" 
                           name="is_active" value="1" {{ old('is_active', $college->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update College
                </button>
                <a href="{{ route('admin.colleges.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

