@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">
            <span class="material-icons-outlined" style="vertical-align: middle;">public</span>
            <span style="vertical-align: middle;">SDG {{ $sdgNumber }} Mapping</span>
        </h3>
        <a href="{{ route('admin.sdg-mappings.index') }}" class="btn btn-outline-secondary btn-sm">
            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_back</span>
            <span style="vertical-align: middle;">Back to List</span>
        </a>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <span class="badge badge-info" style="font-size: 14px;">Total Contributions: {{ $contributions->count() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contributions as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ ucfirst($item->type ?? 'N/A') }}</td>
                            <td>{{ $item->submitter->name ?? 'N/A' }}</td>
                            <td>
                                @if($item->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif(in_array($item->status, ['pending_coordinator', 'pending_dean']))
                                    <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($item->status ?? 'N/A') }}</span>
                                @endif
                            </td>
                            <td>{{ $item->submitted_at ? $item->submitted_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.sdg-contributions.show', $item->id) }}" title="View Contribution" aria-label="View Contribution">
                                    <span class="material-icons-outlined">visibility</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No contributions mapped to this SDG.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
