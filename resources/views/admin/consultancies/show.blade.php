@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'consultancy')
        ->where('submission_id', $consultancy->id)
        ->with(['submitter', 'assignee', 'history.performer'])
        ->first();

    $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'consultancy')
        ->where('submission_id', $consultancy->id)
        ->with('uploader')
        ->get();
@endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <span class="material-icons-outlined" style="vertical-align: middle;">business_center</span>
            <span style="vertical-align: middle;">Consultancy/KT Details</span>
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{{ $consultancy->project_consultancy_name }}</h4>
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Client/Sponsor</th>
                        <td>{{ $consultancy->client_sponsor ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Income Type</th>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($consultancy->income_type ?? 'N/A') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Year</th>
                        <td>{{ $consultancy->year ?? ($consultancy->start_date ? $consultancy->start_date->format('Y') : 'N/A') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($consultancy->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif(in_array($consultancy->status, ['pending_coordinator', 'pending_dean']))
                                <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $consultancy->status)) }}</span>
                            @elseif($consultancy->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @elseif($consultancy->status == 'draft')
                                <span class="badge badge-secondary">Draft</span>
                            @else
                                <span class="badge badge-info">{{ ucfirst($consultancy->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($consultancy->start_date)
                    <tr>
                        <th>Start Date</th>
                        <td>{{ $consultancy->start_date->format('F d, Y') }}</td>
                    </tr>
                    @endif
                    @if($consultancy->end_date)
                    <tr>
                        <th>End Date</th>
                        <td>{{ $consultancy->end_date->format('F d, Y') }}</td>
                    </tr>
                    @endif
                    @if($consultancy->amount_omr)
                    <tr>
                        <th>Amount (OMR)</th>
                        <td><strong>{{ number_format($consultancy->amount_omr, 2) }} OMR</strong></td>
                    </tr>
                    @endif
                    @if($consultancy->submitter)
                    <tr>
                        <th>Submitted By</th>
                        <td>{{ $consultancy->submitter->name }} ({{ $consultancy->submitter->email ?? 'N/A' }})</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Points Allocated</th>
                        <td>
                            @if($consultancy->points_allocated)
                                <strong style="color: var(--primary); font-size: 1.2em;">{{ number_format($consultancy->points_allocated, 2) }}</strong>
                                @if($consultancy->points_locked)
                                    <span class="badge badge-info">
                                        <span class="material-icons-outlined" style="font-size:14px;vertical-align:middle;">lock</span>
                                        Locked
                                    </span>
                                @endif
                            @else
                                <span class="text-muted">Not calculated yet</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

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
                                <td>{{ $workflow->assignee->name }} ({{ $workflow->assignee->email ?? 'N/A' }})</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">attach_file</span>
                            <span style="vertical-align: middle;">Evidence Files & Attachments ({{ $evidenceFiles->count() }})</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($evidenceFiles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Uploaded</th>
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
                                            @else
                                                <span class="badge badge-secondary">{{ strtoupper(pathinfo($file->file_name, PATHINFO_EXTENSION)) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($file->file_type !== 'text/url')
                                                {{ number_format($file->file_size / 1024, 2) }} KB
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $file->uploaded_at ? $file->uploaded_at->format('M d, Y') : 'N/A' }}</td>
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
                        @else
                        <div style="text-align:center;padding:2rem;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
                            <span class="material-icons-outlined" style="font-size:48px;color:#d1d5db;">folder_open</span>
                            <p style="color:#6b7280;margin-top:1rem;margin-bottom:0;">No evidence files have been uploaded yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="{{ route('admin.consultancies.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_back</span>
                <span style="vertical-align: middle;">Back to List</span>
            </a>
            @php
                $workflowStatus = $workflow->status ?? null;
                $workflowCompleted = $workflowStatus && in_array($workflowStatus, ['approved', 'rejected']);
                $user = auth()->user();

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

                $canShowActions = !in_array($consultancy->status, ['approved', 'rejected'])
                                  && !$workflowCompleted
                                  && in_array($consultancy->status, ['submitted', 'pending_coordinator', 'pending_dean'])
                                  && $canApprove;
            @endphp
            @if($canShowActions)
                <form action="{{ route('admin.consultancies.approve', $consultancy->id) }}" method="POST" style="display: inline;" class="approve-consultancy-show-form">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm approve-consultancy-btn">
                        <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">check_circle</span>
                        <span style="vertical-align: middle;">Approve Consultancy</span>
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="showRejectModal()">
                    <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">cancel</span>
                    <span style="vertical-align: middle;">Reject Consultancy</span>
                </button>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.consultancies.reject', $consultancy->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Consultancy/KT</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for Rejection</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showRejectModal() {
        $('#rejectModal').modal('show');
    }

    $(document).on('submit', '.approve-consultancy-show-form', function (e) {
        e.preventDefault();
        const form = $(this);
        Swal.fire({
            title: 'Approve Consultancy?',
            text: 'Are you sure you want to approve this consultancy?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve',
            confirmButtonColor: '#22c55e'
        }).then((result) => {
            if (result.isConfirmed) {
                form[0].submit();
            }
        });
    });
</script>
@endsection
