@extends('layouts.admin')

@section('page-title', 'All Publications')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Publications</h3>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="mb-3">
            <form method="GET" action="{{ route('faculty.publications.all') }}" class="d-flex gap-2 flex-wrap">
                <select name="status" class="form-control" style="width: auto;">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}" style="width: auto;">
                <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}" style="width: auto;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('faculty.publications.all') }}" class="btn btn-outline-secondary">Clear</a>
            </form>
        </div>

        <!-- Publications Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="publicationsTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
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
                        <td>{{ $publication->primaryAuthor->name ?? $publication->submitter->name ?? 'N/A' }}</td>
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
                            <a href="{{ route('faculty.publications.show', $publication) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No publications found.</td>
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
            order: [[4, 'desc']]
        });
    });
</script>
@endpush
@endsection

