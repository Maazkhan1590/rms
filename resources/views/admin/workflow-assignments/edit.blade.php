@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Edit Workflow Assignment</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.workflow-assignments.update', $workflowAssignment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_id">User <span class="text-danger">*</span></label>
                        <select class="form-control @error('user_id') is-invalid @enderror" 
                                id="user_id" name="user_id" required>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ old('user_id', $workflowAssignment->user_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role">Role <span class="text-danger">*</span></label>
                        <select class="form-control @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $workflowAssignment->role) == $role ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="college">College</label>
                        <select class="form-control @error('college') is-invalid @enderror" 
                                id="college" name="college">
                            <option value="">All Colleges (Global Assignment)</option>
                            @foreach($colleges as $name => $display)
                                <option value="{{ $name }}" {{ old('college', $workflowAssignment->college) == $name ? 'selected' : '' }}>
                                    {{ $display }}
                                </option>
                            @endforeach
                        </select>
                        @error('college')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select class="form-control @error('department') is-invalid @enderror" 
                                id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $name => $display)
                                <option value="{{ $name }}" {{ old('department', $workflowAssignment->department) == $name ? 'selected' : '' }}>
                                    {{ $display }}
                                </option>
                            @endforeach
                        </select>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $workflowAssignment->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active (Assignment is currently in effect)
                    </label>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Assignment
                </button>
                <a href="{{ route('admin.workflow-assignments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
