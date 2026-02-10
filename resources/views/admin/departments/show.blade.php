@extends('layouts.admin')

@section('page-title', 'Department Details')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $department->name }}</strong>
            <small class="text-muted d-block">Department Code: {{ $department->code ?? 'N/A' }}</small>
        </div>
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group btn-group-sm mr-2" role="group">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary" title="Back to list" aria-label="Back to list">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                @can('college_update')
                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-outline-primary" title="Edit department" aria-label="Edit department">
                    <span class="material-icons-outlined">edit</span>
                </a>
                @endcan
                <a href="{{ route('admin.colleges.show', $department->college) }}" class="btn btn-outline-info" title="View college" aria-label="View college">
                    <span class="material-icons-outlined">apartment</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Department Overview</h5>
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 35%;">Code</th>
                            <td>{{ $department->code ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($department->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>College</th>
                            <td>
                                <a href="{{ route('admin.colleges.show', $department->college) }}">
                                    {{ $department->college->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Coordinator</th>
                            <td>{{ $department->coordinator->name ?? 'Not Assigned' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Faculty Overview</h5>
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 40%;">Total Faculty</th>
                            <td>{{ $department->users->count() }}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td>
                                @if($department->users->count() > 0)
                                    <span class="text-muted">Faculty members are assigned to this department.</span>
                                @else
                                    <span class="text-muted">No faculty members assigned to this department.</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

