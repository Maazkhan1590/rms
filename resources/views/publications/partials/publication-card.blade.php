@foreach($publications as $publication)
<div class="publication-card" data-publication-id="{{ $publication->id }}" onclick="window.location.href='{{ route('publications.show', $publication->id) }}'">
    <div class="publication-header">
        <h3 class="publication-title">{{ $publication->title }}</h3>
        <div class="publication-authors">
            <i class="fas fa-user-edit"></i>
            {{ $publication->submitter->name ?? 'Anonymous' }}
            @if($publication->primaryAuthor)
                , {{ $publication->primaryAuthor->name }}
            @endif
        </div>
    </div>
    <div class="publication-body">
        <p class="publication-abstract">
            {{ Str::limit(strip_tags($publication->abstract ?? ''), 200) }}
        </p>
    </div>
    <div class="publication-footer">
        <span class="publication-category">{{ strtoupper(str_replace('_', ' ', $publication->publication_type ?? 'Publication')) }}</span>
        <div class="publication-meta">
            <div class="publication-date">
                <i class="far fa-calendar"></i>
                {{ $publication->published_at ? $publication->published_at->format('F d, Y') : ($publication->publication_year ?? 'N/A') }}
            </div>
        </div>
        <div class="publication-actions">
            <button class="btn btn-outline view-publication-btn" data-id="{{ $publication->id }}" onclick="event.stopPropagation();">
                <i class="fas fa-eye"></i> View Details
            </button>
        </div>
    </div>
</div>
@endforeach
