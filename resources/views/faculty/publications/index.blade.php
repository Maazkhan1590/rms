@extends('layouts.admin')

@section('page-title', 'My Publications')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">My Publications</h3>
            <a href="{{ route('faculty.publications.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Submit New Publication
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="mb-3">
            <form method="GET" action="{{ route('faculty.publications.index') }}" class="d-flex gap-2 flex-wrap">
                <select name="status" class="form-control" style="width: auto;">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}" style="width: auto;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('faculty.publications.index') }}" class="btn btn-outline-secondary">Clear</a>
            </form>
        </div>

        <!-- Publications Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="publicationsTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Journal/Category</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($publications as $publication)
                    <tr>
                        <td>
                            <a href="{{ route('faculty.publications.show', $publication) }}">
                                {{ Str::limit($publication->title, 50) }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) }}</span>
                        </td>
                        <td>
                            {{ $publication->journal_name ?? $publication->conference_name ?? 'N/A' }}
                            @if($publication->quartile)
                                <span class="badge badge-success">{{ $publication->quartile }}</span>
                            @endif
                        </td>
                        <td>{{ $publication->year ?? 'N/A' }}</td>
                        <td>
                            @if($publication->status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif($publication->status === 'submitted')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($publication->status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-secondary">Draft</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ number_format($publication->points_allocated ?? 0, 1) }}</strong>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('faculty.publications.show', $publication) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($publication->status === 'draft')
                                <a href="{{ route('faculty.publications.edit', $publication) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('faculty.publications.submit', $publication) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Submit this publication for approval?')">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No publications found. <a href="{{ route('faculty.publications.create') }}">Submit your first publication</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $publications->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#publicationsTable').DataTable({
            paging: false,
            searching: false,
            info: false,
            order: [[3, 'desc']]
        });
    });
</script>
@endpush
@endsection

