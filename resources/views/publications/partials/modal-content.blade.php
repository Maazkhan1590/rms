<div class="publication-detail-content">
    <div class="publication-detail-header">
        <span class="publication-detail-category">{{ strtoupper(str_replace('_', ' ', $publication->publication_type ?? 'Publication')) }}</span>
        <h2>{{ $publication->title }}</h2>
        <div class="publication-detail-authors">
            <i class="fas fa-user-edit"></i>
            @if($publication->authors && is_array($publication->authors))
                {{ implode(', ', array_column($publication->authors, 'name')) }}
            @else
                {{ $publication->submitter->name ?? 'Anonymous' }}
                @if($publication->primaryAuthor)
                    , {{ $publication->primaryAuthor->name }}
                @endif
            @endif
        </div>
    </div>
    
    <div class="publication-detail-meta">
        <div class="meta-item">
            <i class="fas fa-calendar-alt"></i>
            <span>Published: {{ $publication->published_at ? $publication->published_at->format('F d, Y') : ($publication->publication_year ?? 'N/A') }}</span>
        </div>
        @if($publication->journal_name)
        <div class="meta-item">
            <i class="fas fa-book"></i>
            <span>Journal: {{ $publication->journal_name }}</span>
        </div>
        @endif
        @if($publication->conference_name)
        <div class="meta-item">
            <i class="fas fa-users"></i>
            <span>Conference: {{ $publication->conference_name }}</span>
        </div>
        @endif
        @if($publication->doi)
        <div class="meta-item">
            <i class="fas fa-hashtag"></i>
            <span>DOI: <a href="https://doi.org/{{ $publication->doi }}" target="_blank">{{ $publication->doi }}</a></span>
        </div>
        @endif
        @if($publication->publisher)
        <div class="meta-item">
            <i class="fas fa-building"></i>
            <span>Publisher: {{ $publication->publisher }}</span>
        </div>
        @endif
        @if($publication->isbn)
        <div class="meta-item">
            <i class="fas fa-barcode"></i>
            <span>ISBN: {{ $publication->isbn }}</span>
        </div>
        @endif
    </div>
    
    @if($publication->abstract)
    <div class="publication-detail-abstract">
        <h3><i class="fas fa-file-alt"></i> Abstract</h3>
        <p>{{ $publication->abstract }}</p>
    </div>
    @endif
    
    @if($publication->authors && is_array($publication->authors) && count($publication->authors) > 0)
    <div class="publication-detail-keywords">
        <h3><i class="fas fa-users"></i> Authors</h3>
        <div class="keywords-list">
            @foreach($publication->authors as $author)
            <span class="keyword">{{ $author['name'] ?? $author }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="publication-detail-actions">
        @if($publication->published_link)
        <a href="{{ $publication->published_link }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-external-link-alt"></i> View Publication
        </a>
        @endif
        @if($publication->proceedings_link)
        <a href="{{ $publication->proceedings_link }}" target="_blank" class="btn btn-outline">
            <i class="fas fa-file-pdf"></i> View Proceedings
        </a>
        @endif
        <a href="{{ route('publications.show', $publication->id) }}" class="btn btn-outline">
            <i class="fas fa-external-link-alt"></i> Full Page View
        </a>
    </div>
</div>
