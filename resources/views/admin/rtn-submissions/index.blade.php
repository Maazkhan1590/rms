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
                    <i class="fas fa-file-alt"></i> RTN Submissions Management
                </h3>
            </div>
            <div style="margin-top: 10px; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('rtn-submissions.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Create RTN Submission
                </a>
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}" 
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    <i class="fas fa-clock"></i> Pending
                </a>
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'approved'])) }}" 
                   class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-check"></i> Approved
                </a>
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'rejected'])) }}" 
                   class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fas fa-times"></i> Rejected
                </a>
                <a href="{{ route('admin.rtn-submissions.index', request()->except('status')) }}" 
                   class="btn btn-sm {{ !request('status') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-list"></i> All
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.rtn-submissions.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search RTN submissions..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($types ?? [] as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ strtoupper($type) }}
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
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.rtn-submissions.index') }}" class="btn btn-secondary">
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
                        <th>Title</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Workflow</th>
                        <th>Points</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr data-entry-id="{{ $submission->id }}">
                            <td>{{ $submission->id }}</td>
                            <td>
                                <strong>{{ Str::limit($submission->title, 50) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ strtoupper($submission->rtn_type ?? 'N/A') }}
                                </span>
                            </td>
                            <td>
                                @if($submission->user)
                                    {{ $submission->user->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $submission->year ?? 'N/A' }}</td>
                            <td>
                                @if($submission->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($submission->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($submission->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($submission->status == 'submitted')
                                    <span class="badge badge-info">Submitted</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($submission->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'rtn')
                                        ->where('submission_id', $submission->id)
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
                                @if($submission->points)
                                    <strong style="color: var(--primary);">{{ number_format($submission->points, 2) }}</strong>
                                    <br><small class="text-muted">(RTN-3/RTN-4: 5 points)</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($submission->submitted_at)
                                    {{ $submission->submitted_at->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.rtn-submissions.show', $submission->id) }}" title="View" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if(in_array($submission->status, ['pending', 'submitted', 'draft', 'pending_coordinator', 'pending_dean']))
                                        <form action="{{ route('admin.rtn-submissions.approve', $submission->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Approve this RTN submission? This will allocate 5 points.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #22c55e; color: white; border: none; cursor: pointer;">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $submission->id }})" title="Reject" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #ef4444; color: white; border: none; cursor: pointer;">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.rtn-submissions.destroy', $submission->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #ef4444; color: white; border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No RTN submissions found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($submissions->hasPages())
            <div style="margin-top: 20px;">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject RTN Submission</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_reason">Reason (Optional)</label>
                        <textarea class="form-control" id="reject_reason" name="reason" rows="3" 
                                  placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Submission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showRejectModal(submissionId) {
        const form = document.getElementById('rejectForm');
        form.action = '/admin/rtn-submissions/' + submissionId + '/reject';
        $('#rejectModal').modal('show');
    }
</script>
@endsection
