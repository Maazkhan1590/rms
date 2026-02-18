@foreach($fellows as $fellow)
<div class="publication-card">
    <div class="publication-card-header">
        <span class="publication-category">RESEARCH FELLOW</span>
        @if($fellow->workflow_status == 'approved')
            <span class="publication-status approved">Approved</span>
        @endif
    </div>
    <div class="publication-card-body">
        <h3 class="publication-card-title">
            <a href="{{ route('research-fellows.show', $fellow->id) }}">
                {{ Str::limit($fellow->publication_title, 150) }}
            </a>
        </h3>
        @if($fellow->journal)
            <p class="publication-card-abstract">
                <strong>Journal:</strong> {{ $fellow->journal }}
            </p>
        @endif
        <div class="publication-card-meta">
            @if($fellow->year)
                <span><i class="fas fa-calendar-alt"></i> {{ $fellow->year }}</span>
            @endif
            @if($fellow->doi)
                <span><i class="fas fa-link"></i> DOI: {{ $fellow->doi }}</span>
            @endif
            @if($fellow->submitter)
                <span><i class="fas fa-user"></i> {{ $fellow->submitter->name }}</span>
            @endif
        </div>
    </div>
    <div class="publication-card-footer">
        <a href="{{ route('research-fellows.show', $fellow->id) }}" class="btn btn-outline btn-sm">
            View Details <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
@endforeach
