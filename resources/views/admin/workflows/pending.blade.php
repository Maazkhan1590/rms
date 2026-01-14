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
                    <i class="fas fa-clock"></i> Pending Approvals
                </h3>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="badge badge-warning" style="margin-left: 10px;">{{ $pendingCount }} Pending</span>
                @endif
            </div>
            <div style="margin-top: 10px;">
                <a href="{{ route('admin.workflows.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-list"></i> All Workflows
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.workflows.pending') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($types ?? [] as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-check-label">
                        <input type="checkbox" name="my_workflows" value="1" {{ request('my_workflows') ? 'checked' : '' }}>
                        Show only my assigned workflows
                    </label>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.workflows.pending') }}" class="btn btn-secondary">
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
                        <th>Type</th>
                        <th>Submission</th>
                        <th>Submitted By</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Step</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workflows as $workflow)
                        <tr data-entry-id="{{ $workflow->id }}">
                            <td>{{ $workflow->id }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst($workflow->submission_type) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $submission = $workflow->submission;
                                @endphp
                                @if($submission)
                                    @if($workflow->submission_type == 'publication')
                                        <strong>{{ Str::limit($submission->title ?? 'N/A', 40) }}</strong>
                                    @elseif($workflow->submission_type == 'grant')
                                        <strong>{{ Str::limit($submission->title ?? 'N/A', 40) }}</strong>
                                    @else
                                        <strong>ID: {{ $submission->id ?? 'N/A' }}</strong>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($workflow->submitter)
                                    {{ $workflow->submitter->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($workflow->assignee)
                                    <strong>{{ $workflow->assignee->name }}</strong>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($workflow->status == 'pending_coordinator')
                                    <span class="badge badge-warning">Pending Coordinator</span>
                                @elseif($workflow->status == 'pending_dean')
                                    <span class="badge badge-info">Pending Dean</span>
                                @elseif($workflow->status == 'submitted')
                                    <span class="badge badge-secondary">Submitted</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $workflow->status)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($workflow->current_step == 1)
                                    Faculty
                                @elseif($workflow->current_step == 2)
                                    Coordinator
                                @elseif($workflow->current_step == 3)
                                    Dean
                                @else
                                    Step {{ $workflow->current_step }}
                                @endif
                            </td>
                            <td>
                                {{ $workflow->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.workflows.show', $workflow->id) }}" title="View" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.workflows.approve', $workflow->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Approve this workflow?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #22c55e; color: white; border: none; cursor: pointer;">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $workflow->id }})" title="Reject" style="padding: 4px 8px; font-size: 12px; line-height: 1.5; border-radius: 3px; display: inline-flex; align-items: center; gap: 4px; background-color: #ef4444; color: white; border: none; cursor: pointer;">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No pending workflows found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($workflows->hasPages())
            <div style="margin-top: 20px;">
                {{ $workflows->links() }}
            </div>
        @endif
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
            <form id="rejectForm" method="POST">
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

<script>
    function showRejectModal(workflowId) {
        const form = document.getElementById('rejectForm');
        form.action = '/admin/workflows/' + workflowId + '/reject';
        $('#rejectModal').modal('show');
    }
</script>
@endsection
