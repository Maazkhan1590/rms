@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-plus"></i> Create Workflow Assignment</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.workflow-assignments.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_id">User <span class="text-danger">*</span></label>
                        <select class="form-control @error('user_id') is-invalid @enderror" 
                                id="user_id" name="user_id" required>
                            <option value="">Select User</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Select a user with Coordinator, Dean, or Admin role</small>
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
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Role in the approval workflow</small>
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
                                <option value="{{ $name }}" {{ old('college') == $name ? 'selected' : '' }}>
                                    {{ $display }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Leave empty for global assignment (all colleges)</small>
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
                                <option value="{{ $name }}" {{ old('department') == $name ? 'selected' : '' }}>
                                    {{ $display }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Leave empty for all departments</small>
                        @error('department')
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
                        Active (Assignment is currently in effect)
                    </label>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Create Assignment
                </button>
                <a href="{{ route('admin.workflow-assignments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Workflow Information -->
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-info-circle"></i> Workflow Assignment Guidelines</h5>
    </div>
    <div class="card-body">
        <h6>Default Workflow Steps:</h6>
        <ol>
            <li><strong>Faculty</strong> - Submits research item</li>
            <li><strong>Research Coordinator</strong> - Reviews and approves/rejects</li>
            <li><strong>Dean</strong> - Final review and approval</li>
            <li><strong>Approved</strong> - Item is approved and points are allocated</li>
        </ol>

        <h6 class="mt-3">Fallback Workflow:</h6>
        <p>If no Research Coordinator is assigned for a college/department, the workflow automatically skips to Dean.</p>

        <h6 class="mt-3">Assignment Scope:</h6>
        <ul>
            <li><strong>Global Assignment:</strong> Leave College and Department empty - applies to all</li>
            <li><strong>College-Specific:</strong> Select a college - applies to all departments in that college</li>
            <li><strong>Department-Specific:</strong> Select both college and department - applies only to that department</li>
        </ul>
    </div>
</div>
@endsection
