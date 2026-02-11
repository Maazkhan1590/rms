@extends('layouts.public')

@section('title', $publication->title . ' | Academic Research Portal')

@section('content')
<section class="auth-section" style="padding-top: 4rem; padding-bottom: 4rem;">
    <div class="container">
        <div class="auth-container" style="max-width: 960px; box-shadow: 0 18px 40px rgba(15,23,42,0.12); border-radius: 18px;">
            <div class="auth-header" style="margin-bottom: 1.5rem;">
                <a href="{{ route('publications.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text-light); text-decoration: none; margin-bottom: 1.25rem; font-weight: 500;">
                    <i class="fas fa-arrow-left"></i> Back to Publications
                </a>
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1.5rem;">
                    <div style="flex:1;">
                        <h1 style="font-size: 2rem; margin-bottom: 0.35rem; color: var(--text-color); line-height: 1.3;">
                            {{ $publication->title ?? 'Untitled Publication' }}
                        </h1>
            @php
                $authorNames = [];
                if ($publication->authors && is_array($publication->authors)) {
                    foreach ($publication->authors as $author) {
                        $authorNames[] = $author['name'] ?? (is_string($author) ? $author : '');
                    }
                }
                if (empty($authorNames) && $publication->submitter) {
                    $authorNames[] = $publication->submitter->name;
                }
                if (empty($authorNames) && $publication->primaryAuthor) {
                    $authorNames[] = $publication->primaryAuthor->name;
                }
            @endphp
                        @if(!empty($authorNames))
                        <div style="display:flex; flex-wrap:wrap; gap:0.75rem; color: var(--text-light); margin-bottom: .5rem; font-size:.95rem;">
                            <span style="font-weight:600;">Authors:</span>
                            <span>{{ implode(', ', array_filter($authorNames)) }}</span>
                        </div>
                        @endif
                        <div style="display:flex; flex-wrap:wrap; gap:1.25rem; color: var(--text-light); font-size: 0.9rem; margin-top:.35rem;">
                            @if($publication->publication_year || $publication->year)
                                <div><strong>Year:</strong> {{ $publication->publication_year ?? $publication->year ?? 'N/A' }}</div>
                            @endif
                            @if($publication->journal_name)
                                <div><strong>Journal:</strong> {{ $publication->journal_name }}</div>
                            @endif
                            @if($publication->conference_name)
                                <div><strong>Conference:</strong> {{ $publication->conference_name }}</div>
                            @endif
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="margin-bottom:.5rem;">
                            <span style="display:inline-block; padding:.35rem .9rem; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">
                                {{ strtoupper(str_replace('_', ' ', $publication->publication_type ?? 'Publication')) }}
                            </span>
                        </div>
                        @if($publication->status)
                            <span style="display:inline-block; padding:.35rem .9rem; border-radius:999px; background: {{ $publication->status === 'approved' ? '#22c55e' : ($publication->status === 'submitted' || $publication->status === 'pending' ? '#eab308' : '#6b7280') }}; color:#fff; font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">
                                {{ ucfirst($publication->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div style="margin-top: 2rem;">
                <!-- Abstract Section -->
                <div style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-file-alt" style="color: var(--accent-color);"></i>
                        Abstract
                    </h2>
                    @if($publication->abstract)
                    <p style="color: var(--text-color); line-height: 1.9; font-size: 1.05rem; text-align: justify;">
                        {{ $publication->abstract }}
                    </p>
                    @else
                    <p style="color: var(--text-light); font-style: italic; line-height: 1.9; font-size: 1.05rem;">
                        No abstract available for this publication.
                    </p>
                    @endif
                </div>

                <!-- Publication Details Grid -->
                @php
                    $hasDetails = $publication->journal_name || $publication->journal || $publication->conference_name || 
                                  $publication->publisher || $publication->doi || $publication->isbn || $publication->status;
                @endphp
                @if($hasDetails)
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; padding: 1.75rem 2rem; background: var(--light-color); border-radius: 15px; border: 1px solid var(--border-color);">
                    @if($publication->journal_name || $publication->journal)
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-book" style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <strong style="color: var(--primary-color); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Journal</strong>
                        </div>
                        <p style="color: var(--text-light); margin: 0; font-size: 1rem; line-height: 1.6;">{{ $publication->journal_name ?? $publication->journal ?? 'N/A' }}</p>
                        @if($publication->journal_category)
                        <span style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: var(--accent-color); color: var(--primary-color); border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                            {{ ucfirst(str_replace('_', ' ', $publication->journal_category)) }}
                        </span>
                        @endif
                        @if($publication->quartile)
                        <span style="display: inline-block; margin-top: 0.5rem; margin-left: 0.5rem; padding: 0.25rem 0.75rem; background: var(--primary-color); color: white; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                            {{ $publication->quartile }}
                        </span>
                        @endif
                    </div>
                    @endif

                    @if($publication->conference_name)
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-users" style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <strong style="color: var(--primary-color); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Conference</strong>
                        </div>
                        <p style="color: var(--text-light); margin: 0; font-size: 1rem; line-height: 1.6;">{{ $publication->conference_name }}</p>
                    </div>
                    @endif

                    @if($publication->publisher)
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-building" style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <strong style="color: var(--primary-color); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Publisher</strong>
                        </div>
                        <p style="color: var(--text-light); margin: 0; font-size: 1rem; line-height: 1.6;">{{ $publication->publisher }}</p>
                    </div>
                    @endif

                    @if($publication->doi)
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-hashtag" style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <strong style="color: var(--primary-color); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">DOI</strong>
                        </div>
                        <p style="color: var(--text-light); margin: 0; font-size: 1rem; line-height: 1.6;">
                            <a href="https://doi.org/{{ $publication->doi }}" target="_blank" style="color: var(--accent-color); text-decoration: none; word-break: break-all;">
                                {{ $publication->doi }}
                                <i class="fas fa-external-link-alt" style="font-size: 0.75rem; margin-left: 0.25rem;"></i>
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($publication->isbn)
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-barcode" style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <strong style="color: var(--primary-color); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">ISBN</strong>
                        </div>
                        <p style="color: var(--text-light); margin: 0; font-size: 1rem; line-height: 1.6;">{{ $publication->isbn }}</p>
                    </div>
                    @endif

                    @if($publication->status)
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-info-circle" style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <strong style="color: var(--primary-color); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</strong>
                        </div>
                        <span style="display: inline-block; padding: 0.5rem 1rem; background: {{ $publication->status === 'approved' ? '#22c55e' : ($publication->status === 'pending' || $publication->status === 'submitted' ? '#eab308' : ($publication->status === 'rejected' ? '#ef4444' : '#6b7280')) }}; color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">
                            {{ ucfirst($publication->status) }}
                        </span>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Authors Section -->
                @php
                    $allAuthors = [];
                    if ($publication->authors && is_array($publication->authors)) {
                        foreach ($publication->authors as $author) {
                            $name = $author['name'] ?? (is_string($author) ? $author : '');
                            if ($name) $allAuthors[] = $name;
                        }
                    }
                    if ($publication->co_authors && is_array($publication->co_authors)) {
                        foreach ($publication->co_authors as $coAuthor) {
                            $name = $coAuthor['name'] ?? (is_string($coAuthor) ? $coAuthor : '');
                            if ($name && !in_array($name, $allAuthors)) $allAuthors[] = $name;
                        }
                    }
                    if (empty($allAuthors) && $publication->submitter) {
                        $allAuthors[] = $publication->submitter->name;
                    }
                    if (empty($allAuthors) && $publication->primaryAuthor) {
                        $allAuthors[] = $publication->primaryAuthor->name;
                    }
                @endphp
                @if(!empty($allAuthors))
                <div style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-users" style="color: var(--accent-color);"></i>
                        Authors
                    </h2>
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                        @foreach($allAuthors as $authorName)
                        <div style="background: var(--light-color); padding: 1rem 1.5rem; border-radius: 15px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                            <i class="fas fa-user-circle" style="color: var(--accent-color); font-size: 1.5rem;"></i>
                            <span style="color: var(--text-color); font-weight: 500; font-size: 1rem;">
                                {{ $authorName }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Submission Info -->
                @if($publication->submitter || $publication->college || $publication->department)
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:1.5rem; margin-bottom:2.25rem;">
                    @if($publication->submitter)
                    <div style="padding: 1.5rem; background: var(--light-color); border-radius: 12px; border-left: 4px solid var(--accent-color);">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-user-check" style="color: var(--accent-color);"></i>
                            <strong style="color: var(--primary-color);">Submitted by</strong>
                        </div>
                        <p style="color: var(--text-light); margin: 0; font-size: 1rem;">{{ $publication->submitter->name }}</p>
                        @if($publication->submitted_at)
                        <p style="color: var(--text-lighter); margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                            <i class="far fa-calendar"></i> {{ $publication->submitted_at->format('F d, Y') }}
                        </p>
                        @endif
                    </div>
                    @endif
                    @if($publication->college || $publication->department)
                    <div style="padding: 1.5rem; background: var(--light-color); border-radius: 12px;">
                        <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:.5rem;">
                            <i class="fas fa-university" style="color:var(--accent-color);"></i>
                            <strong style="color: var(--primary-color);">Affiliation</strong>
                        </div>
                        @if($publication->college)
                            <p style="color: var(--text-color); margin: 0 0 .25rem 0; font-size: .95rem;">{{ $publication->college }}</p>
                        @endif
                        @if($publication->department)
                            <p style="color: var(--text-light); margin: 0; font-size: .9rem;">{{ $publication->department }}</p>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                <!-- Action Buttons -->
                @if($publication->published_link || $publication->proceedings_link)
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; padding-top: 2rem; border-top: 2px solid var(--border-color);">
                    @if($publication->published_link)
                    <a href="{{ $publication->published_link }}" target="_blank" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; background: var(--primary-color); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.3s; box-shadow: var(--shadow-sm);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform=''; this.style.boxShadow='var(--shadow-sm)';">
                        <i class="fas fa-external-link-alt"></i>
                        View Publication
                    </a>
                    @endif
                    @if($publication->proceedings_link)
                    <a href="{{ $publication->proceedings_link }}" target="_blank" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; background: transparent; color: var(--accent-color); text-decoration: none; border: 2px solid var(--accent-color); border-radius: 10px; font-weight: 600; transition: all 0.3s;" onmouseover="this.style.background='var(--accent-color)'; this.style.color='var(--primary-color)';" onmouseout="this.style.background='transparent'; this.style.color='var(--accent-color)';">
                        <i class="fas fa-file-pdf"></i>
                        View Proceedings
                    </a>
                    @endif
                </div>
                @endif
                
                <!-- Additional Info -->
                @if($publication->college || $publication->department || $publication->points_allocated)
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--border-color);">
                    <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary-color);">
                        Additional Information
                    </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    @if($publication->college)
                    <div>
                        <strong style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">College</strong>
                        <p style="color: var(--text-color); margin: 0.5rem 0 0 0; font-size: 1rem;">{{ $publication->college }}</p>
                    </div>
                    @endif
                    @if($publication->department)
                    <div>
                        <strong style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Department</strong>
                        <p style="color: var(--text-color); margin: 0.5rem 0 0 0; font-size: 1rem;">{{ $publication->department }}</p>
                    </div>
                    @endif
                    @if($publication->points_allocated)
                    <div>
                        <strong style="color: var(--text-light); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Research Points</strong>
                        <p style="color: var(--text-color); margin: 0.5rem 0 0 0; font-size: 1rem; font-weight: 600;">{{ number_format($publication->points_allocated, 2) }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .auth-container a:hover {
        opacity: 1;
    }
    
    @media (max-width: 768px) {
        .auth-container h1 {
            font-size: 1.75rem !important;
        }
        
        .auth-container {
            padding: 2rem !important;
        }
    }
</style>
@endpush
@endsection
