@extends('layouts.admin')

@section('page-title', 'Publication Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $publication->title }}</h3>
                    <div>
                        @if($publication->status === 'draft')
                            <a href="{{ route('faculty.publications.edit', $publication) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('faculty.publications.submit', $publication) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Submit this publication for approval?')">
                                    <i class="fas fa-paper-plane"></i> Submit for Approval
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if($publication->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($publication->status === 'submitted')
                            <span class="badge badge-warning">Pending Approval</span>
                        @elseif($publication->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            <span class="badge badge-secondary">Draft</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Points Allocated:</strong>
                        <span class="badge badge-info">{{ number_format($publication->points_allocated ?? 0, 1) }}</span>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Publication Type:</strong> {{ ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) }}</p>
                        <p><strong>Journal Category:</strong> {{ ucfirst(str_replace('_', ' ', $publication->journal_category ?? 'N/A')) }}</p>
                        @if($publication->quartile)
                        <p><strong>Quartile:</strong> <span class="badge badge-success">{{ $publication->quartile }}</span></p>
                        @endif
                        <p><strong>Year:</strong> {{ $publication->year ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($publication->journal_name)
                        <p><strong>Journal:</strong> {{ $publication->journal_name }}</p>
                        @endif
                        @if($publication->conference_name)
                        <p><strong>Conference:</strong> {{ $publication->conference_name }}</p>
                        @endif
                        @if($publication->doi)
                        <p><strong>DOI:</strong> <a href="https://doi.org/{{ $publication->doi }}" target="_blank">{{ $publication->doi }}</a></p>
                        @endif
                        @if($publication->isbn)
                        <p><strong>ISBN:</strong> {{ $publication->isbn }}</p>
                        @endif
                    </div>
                </div>

                @if($publication->abstract)
                <hr>
                <div>
                    <strong>Abstract:</strong>
                    <p>{{ $publication->abstract }}</p>
                </div>
                @endif

                @if($publication->published_link)
                <hr>
                <div>
                    <strong>Published Link:</strong>
                    <a href="{{ $publication->published_link }}" target="_blank">{{ $publication->published_link }}</a>
                </div>
                @endif

                @if($publication->workflow)
                <hr>
                <div>
                    <strong>Approval Status:</strong>
                    <p>Current Step: {{ $publication->workflow->current_step == 1 ? 'Faculty' : ($publication->workflow->current_step == 2 ? 'Coordinator' : 'Dean') }}</p>
                    @if($publication->workflow->assignee)
                        <p>Assigned to: {{ $publication->workflow->assignee->name }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @if($publication->workflow && $publication->workflow->history->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Approval History</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($publication->workflow->history as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <strong>{{ $history->performer->name }}</strong>
                            <span class="badge badge-{{ $history->action === 'approved' ? 'success' : ($history->action === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($history->action) }}
                            </span>
                            <p class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</p>
                            @if($history->comments)
                                <p>{{ $history->comments }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('faculty.publications.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                @if($publication->status === 'draft')
                <a href="{{ route('faculty.publications.edit', $publication) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit"></i> Edit Publication
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -8px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--color-primary);
        border: 2px solid white;
    }

    .timeline-content {
        padding-left: 20px;
    }
</style>
@endsection

