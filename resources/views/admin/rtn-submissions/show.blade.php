@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> RTN Submission Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{{ $rtnSubmission->title }}</h4>
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">RTN Type</th>
                        <td>
                            <span class="badge badge-info">
                                {{ strtoupper($rtnSubmission->rtn_type ?? 'N/A') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>User</th>
                        <td>{{ $rtnSubmission->user->name ?? 'N/A' }} ({{ $rtnSubmission->user->email ?? 'N/A' }})</td>
                    </tr>
                    <tr>
                        <th>Year</th>
                        <td>{{ $rtnSubmission->year ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($rtnSubmission->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($rtnSubmission->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($rtnSubmission->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($rtnSubmission->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Points Allocated</th>
                        <td>
                            @if($rtnSubmission->points)
                                <strong style="color: var(--primary); font-size: 1.2em;">{{ number_format($rtnSubmission->points, 2) }}</strong>
                                <small class="text-muted">(RTN-3/RTN-4: 5 points)</small>
                            @else
                                <span class="text-muted">Not calculated yet (will be 5 points when approved)</span>
                            @endif
                        </td>
                    </tr>
                    @if($rtnSubmission->description)
                    <tr>
                        <th>Description</th>
                        <td>{{ $rtnSubmission->description }}</td>
                    </tr>
                    @endif
                    @if($rtnSubmission->student_coauthors && is_array($rtnSubmission->student_coauthors) && count($rtnSubmission->student_coauthors) > 0)
                    <tr>
                        <th>Student Co-authors</th>
                        <td>
                            <ul>
                                @foreach($rtnSubmission->student_coauthors as $coauthor)
                                    <li>{{ $coauthor }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Workflow Information -->
        @php
            $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'rtn')
                ->where('submission_id', $rtnSubmission->id)
                ->with(['submitter', 'assignee', 'history.performer'])
                ->first();
        @endphp
        
        @if($workflow)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-sitemap"></i> Workflow Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Workflow Status</th>
                                <td>
                                    @if($workflow->status == 'pending_coordinator')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-user-tie"></i> Pending Coordinator Approval
                                        </span>
                                    @elseif($workflow->status == 'pending_dean')
                                        <span class="badge badge-info">
                                            <i class="fas fa-user-graduate"></i> Pending Dean Approval
                                        </span>
                                    @elseif($workflow->status == 'approved')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Approved
                                        </span>
                                    @elseif($workflow->status == 'rejected')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $workflow->status)) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($workflow->assignee)
                            <tr>
                                <th>Assigned To</th>
                                <td>{{ $workflow->assignee->name }} ({{ $workflow->assignee->email }})</td>
                            </tr>
                            @endif
                        </table>

                        @if($workflow->history && $workflow->history->count() > 0)
                        <h6 class="mt-3 mb-2"><i class="fas fa-history"></i> Approval History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Action</th>
                                        <th>Performed By</th>
                                        <th>Comments</th>
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Evidence Files Section -->
        @php
            $evidenceFiles = $rtnSubmission->evidenceFiles ?? collect();
        @endphp
        @if($evidenceFiles->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-paperclip"></i> Evidence Files ({{ $evidenceFiles->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Uploaded By</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evidenceFiles as $file)
                                    <tr>
                                        <td>{{ $file->file_name }}</td>
                                        <td>
                                            @if($file->file_type === 'text/url')
                                                <span class="badge badge-info">URL</span>
                                            @elseif(str_contains($file->file_type, 'image'))
                                                <span class="badge badge-success">Image</span>
                                            @elseif(str_contains($file->file_type, 'pdf'))
                                                <span class="badge badge-danger">PDF</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $file->file_type }}</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $file->file_category ?? 'other')) }}</td>
                                        <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                                        <td>{{ $file->uploaded_at ? $file->uploaded_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                        <td>
                                            @if($file->file_type === 'text/url')
                                                <a href="{{ $file->file_path }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-external-link-alt"></i> Open URL
                                                </a>
                                            @else
                                                <a href="{{ Storage::disk('public')->url($file->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.rtn-submissions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @if(in_array($rtnSubmission->status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean']))
                <form action="{{ route('admin.rtn-submissions.approve', $rtnSubmission->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Approve this RTN submission? This will allocate 5 points.');">
                        <i class="fas fa-check"></i> Approve Submission
                    </button>
                </form>
                <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                    <i class="fas fa-times"></i> Reject Submission
                </button>
            @endif
        </div>
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
            <form action="{{ route('admin.rtn-submissions.reject', $rtnSubmission->id) }}" method="POST">
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
    function showRejectModal() {
        $('#rejectModal').modal('show');
    }
</script>
@endsection
