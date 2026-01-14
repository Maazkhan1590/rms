@extends('layouts.admin')

@section('page-title', 'Department Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $department->name }}</h3>
                    <div>
                        @can('college_update')
                        <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Code:</strong> {{ $department->code ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if($department->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <strong>College:</strong> 
                    <a href="{{ route('admin.colleges.show', $department->college) }}">
                        {{ $department->college->name }}
                    </a>
                </div>

                @if($department->coordinator)
                <div class="mb-3">
                    <strong>Coordinator:</strong> {{ $department->coordinator->name }}
                </div>
                @endif

                <hr>

                <h5>Faculty Members ({{ $department->users->count() }})</h5>
                @if($department->users->count() > 0)
                <p class="text-muted">Total faculty members: {{ $department->users->count() }}</p>
                @else
                <p class="text-muted">No faculty members assigned to this department.</p>
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
                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                @can('college_update')
                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit"></i> Edit Department
                </a>
                @endcan
                <a href="{{ route('admin.colleges.show', $department->college) }}" class="btn btn-info btn-block">
                    <i class="fas fa-building"></i> View College
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

