@extends('layouts.admin')

@section('page-title', 'College Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $college->name }}</h3>
                    <div>
                        @can('college_update')
                        <a href="{{ route('admin.colleges.edit', $college) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Code:</strong> {{ $college->code ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if($college->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                @if($college->dean)
                <div class="mb-3">
                    <strong>Dean:</strong> {{ $college->dean->name }}
                </div>
                @endif

                <hr>

                <h5>Departments ({{ $college->departments->count() }})</h5>
                @if($college->departments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
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
                                <td>{{ $dept->code }}</td>
                                <td>{{ $dept->name }}</td>
                                <td>{{ $dept->coordinator->name ?? 'N/A' }}</td>
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
                <p class="text-muted">No departments in this college.</p>
                @endif

                <hr>

                <h5>Faculty Members ({{ $college->users->count() }})</h5>
                @if($college->users->count() > 0)
                <p class="text-muted">Total faculty members: {{ $college->users->count() }}</p>
                @else
                <p class="text-muted">No faculty members assigned to this college.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.colleges.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                @can('college_update')
                <a href="{{ route('admin.colleges.edit', $college) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit"></i> Edit College
                </a>
                @endcan
                @can('college_create')
                <a href="{{ route('admin.departments.create') }}?college_id={{ $college->id }}" class="btn btn-success btn-block">
                    <i class="fas fa-plus"></i> Add Department
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

