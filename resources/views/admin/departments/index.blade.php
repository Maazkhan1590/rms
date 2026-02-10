@extends('layouts.admin')

@section('page-title', 'Departments')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Departments</h3>
            @can('college_create')
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
                <span class="material-icons-outlined">add</span> Add Department
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if($departments->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="departmentsTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>College</th>
                        <th>Coordinator</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $department)
                    <tr>
                        <td><strong>{{ $department->code ?? 'N/A' }}</strong></td>
                        <td>{{ $department->name }}</td>
                        <td>
                            <a href="{{ route('admin.colleges.show', $department->college) }}">
                                {{ $department->college->name ?? 'N/A' }}
                            </a>
                        </td>
                        <td>{{ $department->coordinator->name ?? 'Not Assigned' }}</td>
                        <td>
                            @if($department->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Department actions">
                                @can('college_read')
                                <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-outline-primary" title="View">
                                    <span class="material-icons-outlined">visibility</span>
                                </a>
                                @endcan
                                @can('college_update')
                                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-outline-info" title="Edit">
                                    <span class="material-icons-outlined">edit</span>
                                </a>
                                @endcan
                                @can('college_delete')
                                <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <span class="material-icons-outlined">delete</span>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <p>No departments found. <a href="{{ route('admin.departments.create') }}">Create your first department</a></p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#departmentsTable').DataTable({
            order: [[1, 'asc']]
        });
    });
</script>
@endpush
@endsection


