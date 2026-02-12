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
                    <i class="fas fa-money-bill-wave"></i> Grants Management
                </h3>
            </div>
            <div style="margin-top: 10px; display: flex; gap: 0.5rem; flex-wrap: wrap;">
{{--                <a href="{{ route('grants.create') }}" class="btn btn-sm btn-primary">--}}
{{--                    <span class="material-icons-outlined">add</span> Create Grant--}}
{{--                </a>--}}
                <a href="{{ route('admin.grants.index', array_merge(request()->except('grant_status'), ['grant_status' => 'pending'])) }}"
                   class="btn btn-sm {{ request('grant_status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    <span class="material-icons-outlined">schedule</span> Pending
                </a>
                <a href="{{ route('admin.grants.index', array_merge(request()->except('grant_status'), ['grant_status' => 'submitted'])) }}"
                   class="btn btn-sm {{ request('grant_status') == 'submitted' ? 'btn-info' : 'btn-outline-info' }}">
                    <span class="material-icons-outlined">send</span> Submitted
                </a>
                <a href="{{ route('admin.grants.index', array_merge(request()->except('grant_status'), ['grant_status' => 'approved'])) }}"
                   class="btn btn-sm {{ request('grant_status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                    <span class="material-icons-outlined">check_circle</span> Approved
                </a>
                <a href="{{ route('admin.grants.index', array_merge(request()->except('grant_status'), ['grant_status' => 'rejected'])) }}"
                   class="btn btn-sm {{ request('grant_status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <span class="material-icons-outlined">cancel</span> Rejected
                </a>
                <a href="{{ route('admin.grants.index', request()->except('grant_status')) }}"
                   class="btn btn-sm {{ !request('grant_status') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <span class="material-icons-outlined">list</span> All
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.grants.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search grants..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($types ?? [] as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                {{ strtoupper($role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="year" class="form-control">
                        <option value="">All Years</option>
                        @foreach($years ?? [] as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users ?? [] as $id => $name)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="material-icons-outlined">search</span> Search
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <a href="{{ route('admin.grants.index') }}" class="btn btn-secondary btn-sm">
                        <span class="material-icons-outlined">refresh</span> Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="min-width: 1200px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Role</th>
                        <th>External/Internal</th>
                        <th>Sponsor</th>
                        <th>Amount (OMR)</th>
                        <th>Units</th>
                        <th>User</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Workflow</th>
                        <th>Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grants as $grant)
                        <tr data-entry-id="{{ $grant->id }}">
                            <td>{{ $grant->id }}</td>
                            <td>
                                <strong>{{ Str::limit($grant->title, 50) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst(str_replace('_', ' ', $grant->grant_type ?? 'N/A')) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ strtoupper($grant->role ?? 'N/A') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $grant->external_internal == 'External' ? 'badge-danger' : 'badge-success' }}">
                                    {{ $grant->external_internal ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ Str::limit($grant->sponsor_name ?? $grant->sponsor ?? 'N/A', 30) }}</td>
                            <td>
                                @if($grant->amount_omr)
                                    <strong>{{ number_format($grant->amount_omr, 2) }} OMR</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($grant->units)
                                    <span class="badge badge-info">{{ $grant->units }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($grant->submitter)
                                    {{ $grant->submitter->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $grant->award_year ?? $grant->submission_year ?? 'N/A' }}</td>
                            <td>
                                @if($grant->grant_status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($grant->grant_status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($grant->grant_status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($grant->grant_status == 'submitted')
                                    <span class="badge badge-info">Submitted</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($grant->grant_status) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'grant')
                                        ->where('submission_id', $grant->id)
                                        ->first();
                                @endphp
                                @if($workflow)
                                    @if($workflow->status == 'pending_coordinator')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-user-tie"></i> Coordinator
                                        </span>
                                    @elseif($workflow->status == 'pending_dean')
                                        <span class="badge badge-info">
                                            <i class="fas fa-user-graduate"></i> Dean
                                        </span>
                                    @elseif($workflow->status == 'approved')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Complete
                                        </span>
                                    @elseif($workflow->status == 'rejected')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $workflow->status)) }}</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">No Workflow</span>
                                @endif
                            </td>
                            <td>
                                @if($grant->points_allocated)
                                    <strong style="color: var(--primary);">{{ number_format($grant->points_allocated, 2) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.grants.show', $grant->id) }}" title="View" aria-label="View">
                                        <span class="material-icons-outlined">visibility</span>
                                    </a>
                                    @php
                                        $workflow = $grant->workflow ?? null;
                                        $workflowStatus = $workflow->status ?? null;
                                        $user = auth()->user();
                                        
                                        // Check if user can approve this workflow step (same logic as controller)
                                        $canApprove = false;
                                        if ($workflow) {
                                            if ($workflow->assigned_to == $user->id) {
                                                $canApprove = true;
                                            } elseif ($workflow->status == 'pending_coordinator' && $user->isResearchCoordinator()) {
                                                $canApprove = true;
                                            } elseif ($workflow->status == 'pending_dean' && $user->isDean()) {
                                                $canApprove = true;
                                            }
                                        }
                                        
                                        // Hide approve/reject buttons if:
                                        // 1. Grant status is approved or rejected, OR
                                        // 2. Workflow status is approved or rejected, OR
                                        // 3. User is not authorized to approve at current step
                                        $canShowActions = !in_array($grant->grant_status, ['approved', 'rejected']) 
                                                          && $workflowStatus !== 'approved' 
                                                          && $workflowStatus !== 'rejected'
                                                          && in_array($grant->grant_status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean'])
                                                          && $canApprove;
                                    @endphp
                                    @if($canShowActions)
                                        <form action="{{ route('admin.grants.approve', $grant->id) }}" method="POST" style="display: inline;" class="approve-grant-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve">
                                                <span class="material-icons-outlined">check_circle</span>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-reject-grant" title="Reject" aria-label="Reject" data-grant-id="{{ $grant->id }}">
                                            <span class="material-icons-outlined">cancel</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No grants found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($grants->hasPages())
            <div style="margin-top: 20px;">
                {{ $grants->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
