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
                    <i class="fas fa-code-branch"></i> Policy Versions Management
                </h3>
            </div>
            <div style="margin-top: 10px;">
                @can('policy_create')
                <a href="{{ route('admin.policy-versions.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Version
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <h5><i class="fas fa-info-circle"></i> Policy Versioning</h5>
            <p><strong>Purpose:</strong> Manage year-wise policy versions to ensure historical data integrity.</p>
            <p><strong>Locking:</strong> Approved historical data remains locked. New rules apply prospectively only.</p>
            <p><strong>Active Version:</strong> Only one version can be active at a time. Activating a new version deactivates others.</p>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.policy-versions.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search versions..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="year" class="form-control">
                        <option value="">All Years</option>
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
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
                    <a href="{{ route('admin.policy-versions.index') }}" class="btn btn-secondary">
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
                        <th>Version Number</th>
                        <th>Year</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Policies</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($versions as $version)
                        <tr data-entry-id="{{ $version->id }}">
                            <td>{{ $version->id }}</td>
                            <td><strong>{{ $version->version_number }}</strong></td>
                            <td>{{ $version->year }}</td>
                            <td>{{ Str::limit($version->description ?? 'No description', 60) }}</td>
                            <td>
                                @if($version->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $version->scoringPolicies->count() }} Policies</span>
                            </td>
                            <td>
                                {{ $version->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.policy-versions.show', $version->id) }}" title="View" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @can('policy_update')
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.policy-versions.edit', $version->id) }}" title="Edit" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if(!$version->is_active)
                                    <form action="{{ route('admin.policy-versions.activate', $version->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Activate" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #22c55e; color: white; border: none; cursor: pointer;">
                                            <i class="fas fa-check"></i> Activate
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                    @can('policy_delete')
                                    <form action="{{ route('admin.policy-versions.destroy', $version->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This cannot be undone.');">
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
                            <td colspan="8" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No policy versions found.</p>
                                @can('policy_create')
                                <a href="{{ route('admin.policy-versions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Version
                                </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($versions->hasPages())
            <div style="margin-top: 20px;">
                {{ $versions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
