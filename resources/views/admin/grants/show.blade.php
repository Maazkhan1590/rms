@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <span class="material-icons-outlined" style="vertical-align: middle;">payments</span>
            <span style="vertical-align: middle;">Grant Details</span>
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{{ $grant->title }}</h4>
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Grant Type</th>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst(str_replace('_', ' ', $grant->grant_type ?? 'N/A')) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>
                            <span class="badge badge-secondary">
                                {{ strtoupper($grant->role ?? 'N/A') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Submitter</th>
                        <td>
                            @if($grant->submitter)
                                {{ $grant->submitter->name }} ({{ $grant->submitter->email }})
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Sponsor</th>
                        <td>{{ $grant->sponsor_name ?? $grant->sponsor ?? 'N/A' }}</td>
                    </tr>
                    @if($grant->amount_omr)
                    <tr>
                        <th>Amount (OMR)</th>
                        <td><strong>{{ number_format($grant->amount_omr, 2) }} OMR</strong></td>
                    </tr>
                    @endif
                    @if($grant->units)
                    <tr>
                        <th>Units</th>
                        <td><span class="badge badge-info">{{ $grant->units }}</span></td>
                    </tr>
                    @endif
                    <tr>
                        <th>Award Year</th>
                        <td>{{ $grant->award_year ?? $grant->submission_year ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($grant->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($grant->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($grant->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($grant->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Points Allocated</th>
                        <td>
                            @if($grant->points_allocated)
                                <strong style="color: var(--primary); font-size: 1.2em;">{{ number_format($grant->points_allocated, 2) }}</strong>
                            @else
                                <span class="text-muted">Not calculated yet</span>
                            @endif
                        </td>
                    </tr>
                    @if($grant->summary)
                    <tr>
                        <th>Summary</th>
                        <td>{{ $grant->summary }}</td>
                    </tr>
                    @endif
                    @if($grant->reference_code)
                    <tr>
                        <th>Reference Code</th>
                        <td>{{ $grant->reference_code }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Workflow Information -->
        @php
            $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'grant')
                ->where('submission_id', $grant->id)
                ->with(['submitter', 'assignee', 'history.performer'])
                ->first();
        @endphp
        
        @if($workflow)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">account_tree</span>
                            <span style="vertical-align: middle;">Workflow Information</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Workflow Status</th>
                                <td>
                                    @if($workflow->status == 'pending_coordinator')
                                        <span class="badge badge-warning">
                                            <span class="material-icons-outlined" style="font-size:14px;vertical-align:middle;">supervisor_account</span>
                                            Pending Coordinator Approval
                                        </span>
                                    @elseif($workflow->status == 'pending_dean')
                                        <span class="badge badge-info">
                                            <span class="material-icons-outlined" style="font-size:14px;vertical-align:middle;">school</span>
                                            Pending Dean Approval
                                        </span>
                                    @elseif($workflow->status == 'approved')
                                        <span class="badge badge-success">
                                            <span class="material-icons-outlined" style="font-size:14px;vertical-align:middle;">check_circle</span>
                                            Approved
                                        </span>
                                    @elseif($workflow->status == 'rejected')
                                        <span class="badge badge-danger">
                                            <span class="material-icons-outlined" style="font-size:14px;vertical-align:middle;">cancel</span>
                                            Rejected
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
                        <h6 class="mt-3 mb-2">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">history</span>
                            <span style="vertical-align: middle;">Approval History</span>
                        </h6>
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
            $evidenceFiles = $grant->evidenceFiles ?? collect();
        @endphp
        @if($evidenceFiles->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">attach_file</span>
                            <span style="vertical-align: middle;">Evidence Files ({{ $evidenceFiles->count() }})</span>
                        </h5>
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
                                                <a href="{{ $file->file_path }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">open_in_new</span>
                                                    Open URL
                                                </a>
                                            @else
                                                <a href="{{ Storage::disk('public')->url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">download</span>
                                                    Download
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
            <a href="{{ route('admin.grants.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_back</span>
                <span style="vertical-align: middle;">Back to List</span>
            </a>
            @if(in_array($grant->status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean']))
                <form action="{{ route('admin.grants.approve', $grant->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm approve-grant-btn">
                        <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">check_circle</span>
                        <span style="vertical-align: middle;">Approve Grant</span>
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal()">
                    <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">cancel</span>
                    <span style="vertical-align: middle;">Reject Grant</span>
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
                <h5 class="modal-title">Reject Grant</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.grants.reject', $grant->id) }}" method="POST">
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
                    <button type="submit" class="btn btn-danger">Reject Grant</button>
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
