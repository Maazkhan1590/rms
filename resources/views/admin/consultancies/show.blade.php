@extends('layouts.admin')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <span class="material-icons-outlined" style="vertical-align: middle;">briefcase</span>
            <span style="vertical-align: middle;">Consultancy/KT Details</span>
        </h3>
        <div>
            <a href="{{ route('admin.consultancies.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
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
                        <td>{{ ucfirst($consultancy->income_type ?? 'N/A') }}</td>
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
                        <td>{{ number_format($consultancy->amount_omr, 2) }}</td>
                    </tr>
                    @endif
                    @if($consultancy->submitter)
                    <tr>
                        <th>Submitted By</th>
                        <td>{{ $consultancy->submitter->name }}</td>
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

        @if($consultancy->workflow)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Workflow Status</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Current Status</th>
                                <td>
                                    @if($consultancy->workflow->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($consultancy->workflow->status == 'pending_coordinator')
                                        <span class="badge badge-warning">Pending Coordinator</span>
                                    @elseif($consultancy->workflow->status == 'pending_dean')
                                        <span class="badge badge-info">Pending Dean</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $consultancy->workflow->status)) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($consultancy->workflow->assignee)
                            <tr>
                                <th>Assigned To</th>
                                <td>{{ $consultancy->workflow->assignee->name }}</td>
                            </tr>
                            @endif
                        </table>

                        @if(in_array($consultancy->workflow->status, ['pending_coordinator', 'pending_dean']) && $consultancy->workflow->assigned_to == auth()->id())
                        <div class="mt-3">
                            <form action="{{ route('admin.consultancies.approve', $consultancy->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">
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

        @if($consultancy->evidenceFiles && $consultancy->evidenceFiles->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Evidence Files</h5>
                    </div>
                    <div class="card-body">
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
                                    @foreach($consultancy->evidenceFiles as $file)
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
                                                <a href="{{ $file->file_path }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-external-link-alt"></i> Open
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
@endsection
