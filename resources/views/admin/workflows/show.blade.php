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
        <h3><i class="fas fa-sitemap"></i> Workflow Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Workflow ID</th>
                        <td>{{ $workflow->id }}</td>
                    </tr>
                    <tr>
                        <th>Submission Type</th>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst($workflow->submission_type) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Submission ID</th>
                        <td>{{ $workflow->submission_id }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($workflow->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($workflow->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @elseif($workflow->status == 'pending_coordinator')
                                <span class="badge badge-warning">Pending Coordinator</span>
                            @elseif($workflow->status == 'pending_dean')
                                <span class="badge badge-info">Pending Dean</span>
                            @elseif($workflow->status == 'submitted')
                                <span class="badge badge-secondary">Submitted</span>
                            @elseif($workflow->status == 'returned')
                                <span class="badge badge-warning">Returned</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $workflow->status)) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Current Step</th>
                        <td>
                            @if($workflow->current_step == 1)
                                Faculty Submission
                            @elseif($workflow->current_step == 2)
                                Coordinator Review
                            @elseif($workflow->current_step == 3)
                                Dean Review
                            @else
                                Step {{ $workflow->current_step }}
                            @endif
                        </td>
                    </tr>
                    @if($workflow->submitter)
                    <tr>
                        <th>Submitted By</th>
                        <td>{{ $workflow->submitter->name }} ({{ $workflow->submitter->email }})</td>
                    </tr>
                    @endif
                    @if($workflow->assignee)
                    <tr>
                        <th>Assigned To</th>
                        <td>{{ $workflow->assignee->name }} ({{ $workflow->assignee->email }})</td>
                    </tr>
                    @endif
                    @if($workflow->college)
                    <tr>
                        <th>College</th>
                        <td>{{ $workflow->college }}</td>
                    </tr>
                    @endif
                    @if($workflow->department)
                    <tr>
                        <th>Department</th>
                        <td>{{ $workflow->department }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Created At</th>
                        <td>{{ $workflow->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $workflow->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>

                <!-- Submission Details -->
                @if($submission)
                <div class="mt-4">
                    <h5><i class="fas fa-file-alt"></i> Submission Details</h5>
                    <div class="card">
                        <div class="card-body">
                            @if($workflow->submission_type == 'publication')
                                <p><strong>Title:</strong> {{ $submission->title ?? 'N/A' }}</p>
                                <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $submission->publication_type ?? 'N/A')) }}</p>
                                <p><strong>Year:</strong> {{ $submission->publication_year ?? $submission->year ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> 
                                    @if($submission->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($submission->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($submission->status) }}</span>
                                    @endif
                                </p>
                                @if($submission->points_allocated)
                                <p><strong>Points:</strong> <strong style="color: var(--primary);">{{ number_format($submission->points_allocated, 2) }}</strong></p>
                                @endif
                            @elseif($workflow->submission_type == 'grant')
                                <p><strong>Title:</strong> {{ $submission->title ?? 'N/A' }}</p>
                                <p><strong>Type:</strong> {{ $submission->grant_type ?? 'N/A' }}</p>
                                <p><strong>Year:</strong> {{ $submission->award_year ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> 
                                    @if($submission->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($submission->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($submission->status) }}</span>
                                    @endif
                                </p>
                            @else
                                <p><strong>ID:</strong> {{ $submission->id ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> {{ $submission->status ?? 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Approval History -->
        @if($workflow->history && $workflow->history->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <h5><i class="fas fa-history"></i> Approval History</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Performed By</th>
                                <th>Comments</th>
                                <th>Status Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workflow->history->sortByDesc('created_at') as $history)
                            <tr>
                                <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $history->action == 'approved' ? 'success' : ($history->action == 'rejected' ? 'danger' : 'info') }}">
                                        {{ ucfirst($history->action) }}
                                    </span>
                                </td>
                                <td>{{ $history->performer->name ?? 'N/A' }}</td>
                                <td>{{ $history->comments ?? '-' }}</td>
                                <td>
                                    @if($history->previous_status && $history->new_status)
                                        <small>{{ ucfirst(str_replace('_', ' ', $history->previous_status)) }} → {{ ucfirst(str_replace('_', ' ', $history->new_status)) }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.workflows.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @if(in_array($workflow->status, ['pending_coordinator', 'pending_dean', 'submitted']))
                <form action="{{ route('admin.workflows.approve', $workflow->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve this workflow? This will calculate and assign points if fully approved.');">
                        <i class="fas fa-check"></i> Approve Workflow
                    </button>
                </form>
                <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                    <i class="fas fa-times"></i> Reject Workflow
                </button>
                <button type="button" class="btn btn-warning" onclick="showReturnModal()">
                    <i class="fas fa-undo"></i> Return for Revision
                </button>
                @can('workflow_update')
                <button type="button" class="btn btn-info" onclick="showReassignModal()">
                    <i class="fas fa-user-edit"></i> Reassign Workflow
                </button>
                @endcan
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Workflow</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.workflows.reject', $workflow->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_reason">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_reason" name="reason" rows="3" 
                                  placeholder="Enter reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Workflow</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Workflow for Revision</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.workflows.return', $workflow->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="return_comments">Comments <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="return_comments" name="comments" rows="3" 
                                  placeholder="Enter comments for revision..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Return for Revision</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reassign Modal -->
@can('workflow_update')
<div class="modal fade" id="reassignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reassign Workflow</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.workflows.reassign', $workflow->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Current Assignee:</strong> 
                        {{ $workflow->assignee ? $workflow->assignee->name . ' (' . $workflow->assignee->email . ')' : 'Unassigned' }}
                    </div>
                    <div class="form-group">
                        <label for="reassign_user">Assign To <span class="text-danger">*</span></label>
                        <select class="form-control" id="reassign_user" name="assigned_to" required>
                            <option value="">Select User</option>
                            @foreach($eligibleUsers ?? [] as $userId => $userName)
                                <option value="{{ $userId }}" {{ $workflow->assigned_to == $userId ? 'selected' : '' }}>
                                    {{ $userName }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            @if($workflow->current_step == 2)
                                Select a Research Coordinator or Admin for this step.
                            @elseif($workflow->current_step == 3)
                                Select a Dean or Admin for this step.
                            @else
                                Select an appropriate approver for this workflow step.
                            @endif
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="reassign_reason">Reason (Optional)</label>
                        <textarea class="form-control" id="reassign_reason" name="reason" rows="3" 
                                  placeholder="Enter reason for reassignment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Reassign Workflow</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script>
    function showRejectModal() {
        $('#rejectModal').modal('show');
    }

    function showReturnModal() {
        $('#returnModal').modal('show');
    }
    
    @can('workflow_update')
    function showReassignModal() {
        $('#reassignModal').modal('show');
    }
    @endcan
</script>
@endsection
