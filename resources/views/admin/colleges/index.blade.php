@extends('layouts.admin')

@section('page-title', 'Colleges')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Colleges</h3>
            @can('college_create')
            <a href="{{ route('admin.colleges.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add College
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        @if($colleges->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="collegesTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Dean</th>
                        <th>Departments</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colleges as $college)
                    <tr>
                        <td><strong>{{ $college->code ?? 'N/A' }}</strong></td>
                        <td>{{ $college->name }}</td>
                        <td>{{ $college->dean->name ?? 'Not Assigned' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $college->departments->count() }} Departments</span>
                        </td>
                        <td>
                            @if($college->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                @can('college_read')
                                <a href="{{ route('admin.colleges.show', $college) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('college_update')
                                <a href="{{ route('admin.colleges.edit', $college) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('college_delete')
                                <form action="{{ route('admin.colleges.destroy', $college) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this college?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
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
            <p>No colleges found. <a href="{{ route('admin.colleges.create') }}">Create your first college</a></p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#collegesTable').DataTable({
            order: [[1, 'asc']]
        });
    });
</script>
@endpush
@endsection

