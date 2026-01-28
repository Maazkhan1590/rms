@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-book"></i> Publication Details</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{{ $publication->title }}</h4>
                <p class="text-muted">{{ Str::limit($publication->abstract, 500) }}</p>
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Type</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) }}</td>
                    </tr>
                    <tr>
                        <th>Year</th>
                        <td>{{ $publication->publication_year ?? $publication->year ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($publication->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($publication->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($publication->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($publication->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($publication->journal_name)
                    <tr>
                        <th>Journal</th>
                        <td>{{ $publication->journal_name }}</td>
                    </tr>
                    @endif
                    @if($publication->conference_name)
                    <tr>
                        <th>Conference</th>
                        <td>{{ $publication->conference_name }}</td>
                    </tr>
                    @endif
                    @if($publication->doi)
                    <tr>
                        <th>DOI</th>
                        <td>{{ $publication->doi }}</td>
                    </tr>
                    @endif
                    @if($publication->primaryAuthor)
                    <tr>
                        <th>Primary Author</th>
                        <td>{{ $publication->primaryAuthor->name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Points Allocated</th>
                        <td>
                            @if($publication->points_allocated)
                                <strong style="color: var(--primary); font-size: 1.2em;">{{ number_format($publication->points_allocated, 2) }}</strong>
                                @if($publication->points_locked)
                                    <span class="badge badge-info"><i class="fas fa-lock"></i> Locked</span>
                                @endif
                            @else
                                <span class="text-muted">Not calculated yet</span>
                            @endif
                        </td>
                    </tr>
                    @if($publication->policyVersion)
                    <tr>
                        <th>Policy Version</th>
                        <td>{{ $publication->policyVersion->version }} ({{ $publication->policyVersion->is_active ? 'Active' : 'Inactive' }})</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Evidence Files Section -->
        @php
            $evidenceFiles = $publication->evidenceFiles ?? collect();
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

        <!-- Workflow Information -->
        @php
            $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'publication')
                ->where('submission_id', $publication->id)
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
                            @if($workflow->assignee)
                            <tr>
                                <th>Assigned To</th>
                                <td>{{ $workflow->assignee->name }} ({{ $workflow->assignee->email }})</td>
                            </tr>
                            @endif
                            @if($workflow->submitter)
                            <tr>
                                <th>Submitted By</th>
                                <td>{{ $workflow->submitter->name }} ({{ $workflow->submitter->email }})</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Submitted At</th>
                                <td>{{ $workflow->created_at->format('M d, Y H:i') }}</td>
                            </tr>
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
                                                <small>{{ ucfirst($history->previous_status) }} → {{ ucfirst($history->new_status) }}</small>
                                            @endif
                                        </td>
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
        
        <div style="margin-top: 20px;">
            <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            @can('publication_approve')
                @if(in_array($publication->status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean']))
                    <form action="{{ route('admin.publications.approve', $publication->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Approve this publication? This will calculate and assign points.')">
                            <i class="fas fa-check"></i> Approve Publication
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" onclick="showRejectModal({{ $publication->id }})">
                        <i class="fas fa-times"></i> Reject Publication
                    </button>
                @endif
            @endcan
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Publication</h5>
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
                    <button type="submit" class="btn btn-danger">Reject Publication</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showRejectModal(publicationId) {
        const form = document.getElementById('rejectForm');
        form.action = '/admin/publications/' + publicationId + '/reject';
        $('#rejectModal').modal('show');
    }
</script>
@endsection
