@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users-cog"></i> Workflow Assignment Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">User</th>
                        <td>
                            <strong>{{ $workflowAssignment->user->name ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $workflowAssignment->user->email ?? 'N/A' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst(str_replace('_', ' ', $workflowAssignment->role)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>College</th>
                        <td>
                            @if($workflowAssignment->college)
                                {{ $workflowAssignment->college }}
                            @else
                                <span class="text-muted">All Colleges (Global)</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td>
                            @if($workflowAssignment->department)
                                {{ $workflowAssignment->department }}
                            @else
                                <span class="text-muted">All Departments</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($workflowAssignment->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Assigned By</th>
                        <td>{{ $workflowAssignment->assigner->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Assigned At</th>
                        <td>{{ $workflowAssignment->assigned_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $workflowAssignment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.workflow-assignments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can('workflow_update')
                <a href="{{ route('admin.workflow-assignments.edit', $workflowAssignment->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endcan
        </div>
    </div>
</div>
@endsection
