@extends('layouts.admin')

@section('page-title', 'Edit Department')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Department: {{ $department->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.departments.update', $department) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="college_id">College <span class="text-danger">*</span></label>
                <select class="form-control @error('college_id') is-invalid @enderror" 
                        id="college_id" name="college_id" required>
                    <option value="">Select College</option>
                    @foreach($colleges as $id => $name)
                        <option value="{{ $id }}" {{ old('college_id', $department->college_id) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('college_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="code">Department Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                       id="code" name="code" value="{{ old('code', $department->code) }}" required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Department Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $department->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="coordinator_id">Coordinator</label>
                <select class="form-control @error('coordinator_id') is-invalid @enderror" 
                        id="coordinator_id" name="coordinator_id">
                    <option value="">Select Coordinator</option>
                    @foreach($coordinators as $id => $name)
                        <option value="{{ $id }}" {{ old('coordinator_id', $department->coordinator_id) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('coordinator_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" 
                           name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Department
                </button>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

