@extends('layouts.admin')

@section('page-title', 'College Details')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $college->name }}</strong>
            <small class="text-muted d-block">College Code: {{ $college->code ?? 'N/A' }}</small>
        </div>
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group btn-group-sm mr-2" role="group">
                <a href="{{ route('admin.colleges.index') }}" class="btn btn-outline-secondary" title="Back to list" aria-label="Back to list">
                    <span class="material-icons-outlined">arrow_back</span>
                </a>
            </div>
            <div class="btn-group btn-group-sm" role="group">
                @can('college_update')
                <a href="{{ route('admin.colleges.edit', $college) }}" class="btn btn-outline-primary" title="Edit college" aria-label="Edit college">
                    <span class="material-icons-outlined">edit</span>
                </a>
                @endcan
                @can('college_create')
                <a href="{{ route('admin.departments.create') }}?college_id={{ $college->id }}" class="btn btn-outline-success" title="Add department" aria-label="Add department">
                    <span class="material-icons-outlined">add</span>
                </a>
                @endcan
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">College Overview</h5>
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 35%;">Code</th>
                            <td>{{ $college->code ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($college->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dean</th>
                            <td>{{ $college->dean->name ?? 'Not Assigned' }}</td>
                        </tr>
                        <tr>
                            <th>Total Departments</th>
                            <td>{{ $college->departments->count() }}</td>
                        </tr>
                        <tr>
                            <th>Total Faculty</th>
                            <td>{{ $college->users->count() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Departments</h5>
                @if($college->departments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Coordinator</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($college->departments as $dept)
                            <tr>
                                <td>{{ $dept->code ?? 'N/A' }}</td>
                                <td>{{ $dept->name }}</td>
                                <td>{{ $dept->coordinator->name ?? 'Not Assigned' }}</td>
                                <td>
                                    @if($dept->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No departments in this college yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

