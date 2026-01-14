@extends('layouts.admin')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h3 style="margin: 0; display: inline-block;">
                    <i class="fas fa-users-cog"></i> Workflow Assignments Management
                </h3>
            </div>
            <div style="margin-top: 10px;">
                @can('workflow_create')
                <a href="{{ route('admin.workflow-assignments.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Assignment
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap;">
                <div style="flex: 1;">
                    <h5><i class="fas fa-info-circle"></i> Approval Workflow Configuration</h5>
                    <p><strong>Default Workflow:</strong> Faculty → Research Coordinator → Dean → Approved</p>
                    <p><strong>Fallback Workflow:</strong> Faculty → Dean → Approved (if no coordinator assigned)</p>
                    <p><strong>Auto-escalation:</strong> If approver unavailable, workflows can be manually reassigned by Admin</p>
                    <p><strong>Scope:</strong> Assignments can be college/department-specific or global (leave empty for all)</p>
                </div>
                <div style="margin-top: 10px;">
                    <a href="{{ route('admin.workflow-assignments.visualization') }}" class="btn btn-primary">
                        <i class="fas fa-project-diagram"></i> View Workflow Diagram
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.workflow-assignments.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by user name..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="active" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.workflow-assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>College</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Assigned By</th>
                        <th>Assigned At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr data-entry-id="{{ $assignment->id }}">
                            <td>{{ $assignment->id }}</td>
                            <td>
                                <strong>{{ $assignment->user->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $assignment->user->email ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst(str_replace('_', ' ', $assignment->role)) }}
                                </span>
                            </td>
                            <td>
                                @if($assignment->college)
                                    {{ $assignment->college }}
                                @else
                                    <span class="text-muted">All Colleges</span>
                                @endif
                            </td>
                            <td>
                                @if($assignment->department)
                                    {{ $assignment->department }}
                                @else
                                    <span class="text-muted">All Departments</span>
                                @endif
                            </td>
                            <td>
                                @if($assignment->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{ $assignment->assigner->name ?? 'N/A' }}
                            </td>
                            <td>
                                {{ $assignment->assigned_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.workflow-assignments.show', $assignment->id) }}" title="View" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.workflow-assignments.visualization') }}" title="View Workflow Diagram" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-project-diagram"></i> Diagram
                                    </a>
                                    @can('workflow_update')
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.workflow-assignments.edit', $assignment->id) }}" title="Edit" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @endcan
                                    @can('workflow_delete')
                                    <form action="{{ route('admin.workflow-assignments.destroy', $assignment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #ef4444; color: white; border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No workflow assignments found.</p>
                                @can('workflow_create')
                                <a href="{{ route('admin.workflow-assignments.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Assignment
                                </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assignments->hasPages())
            <div style="margin-top: 20px;">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
