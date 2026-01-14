@extends('layouts.admin')

@section('content')
<style>
    .policies-page-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        overflow: hidden;
    }
    
    .policies-table-container {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .policies-table-wrapper {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
        max-height: 100%;
    }
    
    .policies-table-wrapper table thead {
        position: sticky;
        top: 0;
        background-color: #fff;
        z-index: 10;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }
    
    .policies-table-wrapper table thead th {
        background-color: #fff;
        border-bottom: 2px solid #dee2e6;
    }
</style>
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

<div class="card policies-page-container">
    <div class="card-header" style="flex-shrink: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h3 style="margin: 0; display: inline-block;">
                    <i class="fas fa-calculator"></i> Scoring Policies Management
                </h3>
            </div>
            <div style="margin-top: 10px;">
                @can('policy_create')
                <a href="{{ route('admin.policies.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Policy
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="card-body policies-table-container" style="display: flex; flex-direction: column; overflow: hidden; padding-bottom: 0;">
        <!-- Information Alert -->
        <div class="alert alert-info" style="margin-bottom: 20px; flex-shrink: 0;">
            <h5><i class="fas fa-info-circle"></i> Scoring Policy Guidelines</h5>
            <p><strong>Policy Integrity:</strong> Approved historical data remains locked. New rules apply prospectively only.</p>
            <p><strong>Types:</strong> Publication, Grant, RTN, Bonus Recognition</p>
            <p><strong>Caps:</strong> Set maximum points per category (e.g., Journals: 120, Conferences: 15, Publications Total: 150)</p>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.policies.index') }}" style="margin-bottom: 20px; flex-shrink: 0;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search policies..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($types ?? [] as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
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
                    <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="policies-table-wrapper">
            <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Points</th>
                        <th>Cap</th>
                        <th>Policy Version</th>
                        <th>Effective Period</th>
                        <th>Status</th>
                        <th>Rules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($policies as $policy)
                        <tr data-entry-id="{{ $policy->id }}">
                            <td>{{ $policy->id }}</td>
                            <td><strong>{{ $policy->name }}</strong></td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst($policy->type) }}
                                </span>
                            </td>
                            <td>{{ $policy->category ?? '-' }}</td>
                            <td>{{ $policy->subcategory ?? '-' }}</td>
                            <td>
                                <strong style="color: var(--primary);">{{ number_format($policy->points, 2) }}</strong>
                            </td>
                            <td>
                                @if($policy->cap)
                                    <strong>{{ number_format($policy->cap, 2) }}</strong>
                                @else
                                    <span class="text-muted">No cap</span>
                                @endif
                            </td>
                            <td>
                                @if($policy->policyVersion)
                                    <span class="badge badge-info">
                                        {{ $policy->policyVersion->version_number }} ({{ $policy->policyVersion->year }})
                                    </span>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ $policy->effective_from->format('M Y') }}
                                    @if($policy->effective_to)
                                        → {{ $policy->effective_to->format('M Y') }}
                                    @else
                                        → Ongoing
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($policy->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $policy->rules->count() }} Rules</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.policies.show', $policy->id) }}" title="View" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @can('policy_update')
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.policies.edit', $policy->id) }}" title="Edit" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @endcan
                                    @can('policy_delete')
                                    <form action="{{ route('admin.policies.destroy', $policy->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This cannot be undone.');">
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
                            <td colspan="12" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No scoring policies found.</p>
                                @can('policy_create')
                                <a href="{{ route('admin.policies.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Policy
                                </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        </div>

        <!-- Pagination -->
        @if($policies->hasPages())
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6; flex-shrink: 0;">
                {{ $policies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
