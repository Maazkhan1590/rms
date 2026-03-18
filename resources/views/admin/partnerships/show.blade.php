@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <span class="material-icons-outlined" style="vertical-align: middle;">handshake</span>
            <span style="vertical-align: middle;">Partnership/MOU Details</span>
        </h3>
        <div>
            <a href="{{ route('admin.partnerships.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{{ $partnership->partner_organization }}</h4>
                @if($partnership->scope_theme)
                    <p class="text-muted">{{ Str::limit($partnership->scope_theme, 500) }}</p>
                @endif
                
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Type</th>
                        <td>{{ strtoupper($partnership->type ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <th>Year</th>
                        <td>{{ $partnership->year ?? ($partnership->date_signed ? $partnership->date_signed->format('Y') : 'N/A') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($partnership->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif(in_array($partnership->status, ['pending_coordinator', 'pending_dean']))
                                <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $partnership->status)) }}</span>
                            @elseif($partnership->status == 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @elseif($partnership->status == 'draft')
                                <span class="badge badge-secondary">Draft</span>
                            @else
                                <span class="badge badge-info">{{ ucfirst($partnership->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @if($partnership->date_signed)
                    <tr>
                        <th>Date Signed</th>
                        <td>{{ $partnership->date_signed->format('F d, Y') }}</td>
                    </tr>
                    @endif
                    @if($partnership->expiry_date)
                    <tr>
                        <th>Expiry Date</th>
                        <td>{{ $partnership->expiry_date->format('F d, Y') }}</td>
                    </tr>
                    @endif
                    @if($partnership->leadStaff)
                    <tr>
                        <th>Lead Staff</th>
                        <td>{{ $partnership->leadStaff->name }}</td>
                    </tr>
                    @endif
                    @if($partnership->submitter)
                    <tr>
                        <th>Submitted By</th>
                        <td>{{ $partnership->submitter->name }}</td>
                    </tr>
                    @endif
                    @if($partnership->sdg_s)
                    <tr>
                        <th>SDG Numbers</th>
                        <td>{{ $partnership->sdg_s }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Points Allocated</th>
                        <td>
                            @if($partnership->points_allocated)
                                <strong style="color: var(--primary); font-size: 1.2em;">{{ number_format($partnership->points_allocated, 2) }}</strong>
                                @if($partnership->points_locked)
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

                @if($partnership->scope_theme)
                <div class="mt-3">
                    <h5>Scope/Theme</h5>
                    <p>{{ $partnership->scope_theme }}</p>
                </div>
                @endif

                @if($partnership->outputs_papers_grants_events)
                <div class="mt-3">
                    <h5>Outputs (Papers, Grants, Events)</h5>
                    <p>{{ $partnership->outputs_papers_grants_events }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Workflow Section -->
        @if($partnership->workflow)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">workflow</span>
                            <span style="vertical-align: middle;">Workflow Status</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Current Status</th>
                                <td>
                                    @if($partnership->workflow->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($partnership->workflow->status == 'pending_coordinator')
                                        <span class="badge badge-warning">Pending Coordinator</span>
                                    @elseif($partnership->workflow->status == 'pending_dean')
                                        <span class="badge badge-info">Pending Dean</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $partnership->workflow->status)) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Current Step</th>
                                <td>{{ $partnership->workflow->current_step }}</td>
                            </tr>
                            @if($partnership->workflow->assignee)
                            <tr>
                                <th>Assigned To</th>
                                <td>{{ $partnership->workflow->assignee->name }}</td>
                            </tr>
                            @endif
                        </table>

                        @include('admin.partials.workflow-timeline', ['workflow' => $partnership->workflow])

                        @if(in_array($partnership->workflow->status, ['pending_coordinator', 'pending_dean']) && $partnership->workflow->assigned_to == auth()->id())
                        <div class="mt-3">
                            <form action="{{ route('admin.partnerships.approve', $partnership->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this partnership/MOU?');">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Evidence Files Section -->
        @php
            $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'mou')
                ->where('submission_id', $partnership->id)
                ->with('uploader')
                ->get();
            $hasLinkEvidence = !empty($partnership->evidence_link);
            $totalEvidenceCount = $evidenceFiles->count() + ($hasLinkEvidence ? 1 : 0);
        @endphp
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">attach_file</span>
                            <span style="vertical-align: middle;">Evidence Files & Attachments ({{ $totalEvidenceCount }})</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($partnership->evidence_description)
                        <div class="alert alert-info">
                            <strong>Description:</strong> {{ $partnership->evidence_description }}
                        </div>
                        @endif

                        @if($partnership->evidence_link)
                        <div class="mb-3">
                            <strong>Evidence Link:</strong>
                            <a href="{{ $partnership->evidence_link }}" target="_blank" class="ml-2">
                                {{ $partnership->evidence_link }}
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        @endif

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
                                                    <i class="fas fa-external-link-alt"></i> Open URL
                                                </a>
                                            @else
                                                <a href="{{ Storage::disk('public')->url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @elseif(!$hasLinkEvidence)
                        <p class="text-muted">No evidence files uploaded yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Partnership/MOU</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.partnerships.reject', $partnership->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="comments">Rejection Comments (Optional)</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Partnership/MOU</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
