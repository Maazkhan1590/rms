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

                        @php
                            // Load history entries
                            $historyEntries = $workflow->history ? $workflow->history->sortBy('created_at') : collect();
                            
                            // Check if there's already a draft entry in history
                            $hasDraftEntry = $historyEntries->contains(function($entry) {
                                return isset($entry->new_status) && $entry->new_status == 'draft';
                            });
                            
                            // If no draft entry exists and workflow was created, add virtual draft entry at the beginning
                            if (!$hasDraftEntry && $workflow->created_at) {
                                // Create a virtual draft entry for display (at the beginning of timeline)
                                $draftEntry = new \stdClass();
                                $draftEntry->action = 'submitted';
                                $draftEntry->new_status = 'draft';
                                $draftEntry->previous_status = null;
                                $draftEntry->comments = 'Draft created';
                                $draftEntry->created_at = $workflow->created_at;
                                $draftEntry->performer = $workflow->submitter;
                                
                                // Prepend draft entry to history entries
                                $historyEntries = $historyEntries->prepend($draftEntry)->sortBy(function($entry) {
                                    return is_object($entry->created_at) ? $entry->created_at->timestamp : 
                                           (is_string($entry->created_at) ? strtotime($entry->created_at) : 0);
                                })->values();
                            }
                        @endphp

                        @if($historyEntries->count() > 0)
                        <h6 class="mt-4 mb-3">
                            <span class="material-icons-outlined" style="font-size:20px;vertical-align:middle;color:#4f46e5;">history</span>
                            <span style="vertical-align: middle;font-weight:600;">Approval Timeline</span>
                        </h6>
                        
                        <!-- Professional Timeline -->
                        <div class="approval-timeline">
                            @foreach($historyEntries as $index => $history)
                            @php
                                $action = isset($history->action) ? $history->action : null;
                                $newStatus = isset($history->new_status) ? $history->new_status : null;
                                
                                $isApproved = $action == 'approved';
                                $isRejected = $action == 'rejected';
                                // Check for draft status: either new_status is 'draft' OR action is 'submitted' with draft status
                                $isDraft = ($newStatus == 'draft') || ($action == 'submitted' && $newStatus == 'draft') || ($workflow->status == 'draft' && $action == 'submitted');
                                $isPending = !$isApproved && !$isRejected && !$isDraft;
                                
                                // Set colors and icons based on status
                                if ($isApproved) {
                                    $iconColor = '#22c55e';
                                    $iconBg = '#f0fdf4';
                                    $icon = 'check_circle';
                                } elseif ($isRejected) {
                                    $iconColor = '#ef4444';
                                    $iconBg = '#fef2f2';
                                    $icon = 'cancel';
                                } elseif ($isDraft) {
                                    $iconColor = '#f59e0b';
                                    $iconBg = '#fef3c7';
                                    $icon = 'drafts';
                                } else {
                                    $iconColor = '#3b82f6';
                                    $iconBg = '#eff6ff';
                                    $icon = 'pending';
                                }
                                
                                // Format status names
                                $prevStatus = isset($history->previous_status) && $history->previous_status ? ucwords(str_replace('_', ' ', $history->previous_status)) : null;
                                $newStatusFormatted = isset($history->new_status) && $history->new_status ? ucwords(str_replace('_', ' ', $history->new_status)) : null;
                            @endphp
                            
                            <div class="timeline-item" style="position:relative;padding-left:50px;padding-bottom:30px;">
                                <!-- Timeline Line -->
                                @if(!$loop->last)
                                <div style="position:absolute;left:20px;top:40px;bottom:-10px;width:3px;background:linear-gradient(180deg, {{ $iconColor }} 0%, #e5e7eb 100%);"></div>
                                @endif
                                
                                <!-- Timeline Icon -->
                                <div style="position:absolute;left:0;top:0;width:40px;height:40px;border-radius:50%;background:{{ $iconBg }};border:3px solid {{ $iconColor }};display:flex;align-items:center;justify-content:center;z-index:1;">
                                    <span class="material-icons-outlined" style="font-size:20px;color:{{ $iconColor }};">{{ $icon }}</span>
                                </div>
                                
                                <!-- Timeline Content -->
                                <div style="background:white;border:1px solid #e5e7eb;border-radius:8px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px;">
                                        <div>
                                            <span class="badge" style="background:{{ $iconColor }};color:white;font-size:13px;padding:4px 10px;border-radius:4px;">
                                                {{ ucfirst(isset($history->action) ? $history->action : 'submitted') }}
                                            </span>
                                            @if($prevStatus && $newStatusFormatted && $prevStatus != $newStatusFormatted)
                                            <span style="font-size:12px;color:#6b7280;margin-left:8px;">
                                                <strong>{{ $prevStatus }}</strong> → <strong>{{ $newStatusFormatted }}</strong>
                                            </span>
                                            @elseif($newStatusFormatted && !$prevStatus)
                                            <span style="font-size:12px;color:#6b7280;margin-left:8px;">
                                                <strong>{{ $newStatusFormatted }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <span style="font-size:12px;color:#6b7280;white-space:nowrap;">
                                            <span class="material-icons-outlined" style="font-size:14px;vertical-align:middle;">schedule</span>
                                            {{ is_object($history->created_at) ? $history->created_at->format('M d, Y H:i') : ($workflow->created_at->format('M d, Y H:i') ?? 'N/A') }}
                                        </span>
                                    </div>
                                    
                                    <div style="margin-top:10px;">
                                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                            <span class="material-icons-outlined" style="font-size:18px;color:#4f46e5;">person</span>
                                            <strong style="color:#111827;">{{ isset($history->performer) && is_object($history->performer) ? ($history->performer->name ?? 'N/A') : ($history->performer ?? 'N/A') }}</strong>
                                            @if(isset($history->performer) && is_object($history->performer) && $history->performer->roles && $history->performer->roles->count() > 0)
                                                @php
                                                    $roleNames = $history->performer->roles->pluck('name')->filter()->join(', ');
                                                @endphp
                                                @if($roleNames)
                                                <span style="font-size:12px;color:#6b7280;">
                                                    ({{ $roleNames }})
                                                </span>
                                                @endif
                                            @endif
                                        </div>
                                        
                                        @if(isset($history->comments) && $history->comments)
                                        <div style="margin-top:8px;padding:10px;background:#f9fafb;border-left:3px solid {{ $iconColor }};border-radius:4px;">
                                            <div style="font-size:11px;color:#6b7280;margin-bottom:4px;text-transform:uppercase;font-weight:600;">Comments</div>
                                            <div style="color:#374151;font-size:14px;">{{ $history->comments }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <style>
                            .approval-timeline {
                                margin-top: 20px;
                                padding: 10px 0;
                            }
                            .timeline-item:hover > div:last-child {
                                box-shadow: 0 4px 6px rgba(0,0,0,0.15);
                                transform: translateY(-1px);
                                transition: all 0.2s ease;
                            }
                        </style>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Evidence Files Section -->
        @php
            // Load evidence files directly to avoid any lazy-loading issues
            $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'grant')
                ->where('submission_id', $grant->id)
                ->with('uploader')
                ->get();
        @endphp
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
        </div>
        
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
