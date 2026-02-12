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
{{--                <a href="{{ route('rtn-submissions.create') }}" class="btn btn-sm btn-primary">--}}
{{--                    <span class="material-icons-outlined">add</span> Create RTN Submission--}}
{{--                </a>--}}
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    <span class="material-icons-outlined">schedule</span> Pending
                </a>
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'submitted'])) }}"
                   class="btn btn-sm {{ request('status') == 'submitted' ? 'btn-info' : 'btn-outline-info' }}">
                    <span class="material-icons-outlined">send</span> Submitted
                </a>
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'approved'])) }}"
                   class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                    <span class="material-icons-outlined">check_circle</span> Approved
                </a>
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'rejected'])) }}"
                   class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <span class="material-icons-outlined">cancel</span> Rejected
                </a>
                <a href="{{ route('admin.rtn-submissions.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}"
                   class="btn btn-sm {{ request('status') == 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <span class="material-icons-outlined">edit</span> Draft
                </a>
                <a href="{{ route('admin.rtn-submissions.index', request()->except('status')) }}"
                   class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <span class="material-icons-outlined">list</span> All
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
                                {{ strtoupper(str_replace('_', '-', $type)) }}
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
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="material-icons-outlined">search</span> Search
                    </button>
                    <a href="{{ route('admin.rtn-submissions.index') }}" class="btn btn-secondary btn-sm">
                        <span class="material-icons-outlined">refresh</span> Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="min-width: 1200px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>User</th>
                        <th>Evidence Link</th>
                        <th>Total RTN</th>
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
                                    {{ strtoupper(str_replace('_', '-', $submission->rtn_type ?? 'N/A')) }}
                                </span>
                            </td>
                            <td>
                                @if($submission->user)
                                    {{ $submission->user->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($submission->evidence_link)
                                    <a href="{{ $submission->evidence_link }}" target="_blank" class="text-primary">
                                        <i class="fas fa-link"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($submission->total_rtn)
                                    <span class="badge badge-info">{{ $submission->total_rtn }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $submission->year ?? 'N/A' }}</td>
                            <td>
                                @if($submission->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($submission->status == 'pending' || $submission->status == 'pending_coordinator')
                                    <span class="badge badge-warning">Pending Coordinator</span>
                                @elseif($submission->status == 'pending_dean')
                                    <span class="badge badge-info">Pending Dean</span>
                                @elseif($submission->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($submission->status == 'submitted')
                                    <span class="badge badge-info">Submitted</span>
                                @elseif($submission->status == 'draft')
                                    <span class="badge badge-secondary">Draft</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $submission->status)) }}</span>
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
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.rtn-submissions.show', $submission->id) }}" title="View" aria-label="View">
                                        <span class="material-icons-outlined">visibility</span>
                                    </a>
                                    @php
                                        $workflowStatus = $submission->workflow->status ?? null;
                                        $canShowActions = in_array($submission->status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean']) 
                                                          && $workflowStatus !== 'approved' 
                                                          && $workflowStatus !== 'rejected';
                                    @endphp
                                    @if($canShowActions)
                                        <form action="{{ route('admin.rtn-submissions.approve', $submission->id) }}" method="POST" style="display: inline;" class="rtn-approve-form approve-rtn-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve">
                                                <span class="material-icons-outlined">check_circle</span>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-reject-rtn" title="Reject" aria-label="Reject" data-rtn-id="{{ $submission->id }}">
                                            <span class="material-icons-outlined">cancel</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">
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

@endsection
