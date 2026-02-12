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
                    <i class="fas fa-trophy"></i> Bonus Recognitions Management
                </h3>
            </div>
            <div style="margin-top: 10px; display: flex; gap: 0.5rem; flex-wrap: wrap;">
{{--                <a href="{{ route('bonus-recognitions.create') }}" class="btn btn-sm btn-primary">--}}
{{--                    <span class="material-icons-outlined">add</span> Create Bonus Recognition--}}
{{--                </a>--}}
                <a href="{{ route('admin.bonus-recognitions.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
                   class="btn btn-sm {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                    <span class="material-icons-outlined">schedule</span> Pending
                </a>
                <a href="{{ route('admin.bonus-recognitions.index', array_merge(request()->except('status'), ['status' => 'submitted'])) }}"
                   class="btn btn-sm {{ request('status') == 'submitted' ? 'btn-info' : 'btn-outline-info' }}">
                    <span class="material-icons-outlined">send</span> Submitted
                </a>
                <a href="{{ route('admin.bonus-recognitions.index', array_merge(request()->except('status'), ['status' => 'approved'])) }}"
                   class="btn btn-sm {{ request('status') == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                    <span class="material-icons-outlined">check_circle</span> Approved
                </a>
                <a href="{{ route('admin.bonus-recognitions.index', array_merge(request()->except('status'), ['status' => 'rejected'])) }}"
                   class="btn btn-sm {{ request('status') == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                    <span class="material-icons-outlined">cancel</span> Rejected
                </a>
                <a href="{{ route('admin.bonus-recognitions.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}"
                   class="btn btn-sm {{ request('status') == 'draft' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    <span class="material-icons-outlined">drafts</span> Drafts
                </a>
                <a href="{{ route('admin.bonus-recognitions.index', request()->except('status')) }}"
                   class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-dark' }}">
                    <span class="material-icons-outlined">list</span> All (No Drafts)
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.bonus-recognitions.index') }}" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search recognitions..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($types ?? [] as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $type)) }}
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
                    <a href="{{ route('admin.bonus-recognitions.index') }}" class="btn btn-secondary btn-sm">
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
                        <th>Organization</th>
                        <th>Evidence Link</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Workflow</th>
                        <th>Points</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recognitions as $recognition)
                        <tr data-entry-id="{{ $recognition->id }}">
                            <td>{{ $recognition->id }}</td>
                            <td>
                                <strong>{{ Str::limit($recognition->title, 50) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst(str_replace('_', ' ', $recognition->recognition_type ?? 'N/A')) }}
                                </span>
                            </td>
                            <td>
                                @if($recognition->user)
                                    {{ $recognition->user->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ Str::limit($recognition->organization ?? 'N/A', 30) }}</td>
                            <td>
                                @if($recognition->evidence_link)
                                    <a href="{{ $recognition->evidence_link }}" target="_blank" class="text-primary">
                                        <i class="fas fa-link"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $recognition->year ?? 'N/A' }}</td>
                            <td>
                                @if($recognition->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($recognition->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($recognition->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($recognition->status == 'submitted')
                                    <span class="badge badge-info">Submitted</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($recognition->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'bonus')
                                        ->where('submission_id', $recognition->id)
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
                                @if($recognition->points)
                                    <strong style="color: var(--primary);">{{ number_format($recognition->points, 2) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($recognition->submitted_at)
                                    {{ $recognition->submitted_at->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.bonus-recognitions.show', $recognition->id) }}" title="View" aria-label="View">
                                        <span class="material-icons-outlined">visibility</span>
                                    </a>
                                    @if(in_array($recognition->status, ['pending', 'submitted', 'draft', 'pending_coordinator', 'pending_dean']))
                                        <form action="{{ route('admin.bonus-recognitions.approve', $recognition->id) }}" method="POST" style="display: inline;" class="approve-bonus-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve">
                                                <span class="material-icons-outlined">check_circle</span>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="showRejectModal({{ $recognition->id }})" title="Reject" aria-label="Reject">
                                            <span class="material-icons-outlined">cancel</span>
                                        </button>
                                    @endif
                                    @if($recognition->status !== 'approved')
                                    <form action="{{ route('admin.bonus-recognitions.destroy', $recognition->id) }}" method="POST" style="display: inline;" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" aria-label="Delete">
                                            <span class="material-icons-outlined">delete</span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">
                                <p style="padding: 2rem; color: #6c757d;">No bonus recognitions found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($recognitions->hasPages())
            <div style="margin-top: 20px;">
                {{ $recognitions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Bonus Recognition</h5>
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
                    <button type="submit" class="btn btn-danger">Reject Recognition</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showRejectModal(recognitionId) {
        const form = document.getElementById('rejectForm');
        form.action = '/admin/bonus-recognitions/' + recognitionId + '/reject';
        $('#rejectModal').modal('show');
    }
</script>
@endsection
