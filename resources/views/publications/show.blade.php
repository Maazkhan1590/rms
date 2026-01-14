@extends('layouts.public')

@section('title', $publication->title . ' | Academic Research Portal')

@section('content')
<!-- Publication Detail Header -->
<section class="publication-detail-hero" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); padding: 4rem 0 3rem; margin-top: 80px;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <a href="{{ route('publications.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: white; text-decoration: none; margin-bottom: 2rem; opacity: 0.9; transition: opacity 0.3s;">
                <i class="fas fa-arrow-left"></i> Back to Publications
            </a>
            <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 0.75rem 1.5rem; border-radius: 30px; display: inline-block; margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.2);">
                <span style="color: white; font-weight: 600; text-transform: uppercase; font-size: 0.875rem; letter-spacing: 0.5px;">
                    {{ strtoupper(str_replace('_', ' ', $publication->publication_type ?? 'Publication')) }}
                </span>
            </div>
            <h1 style="font-size: 2.5rem; font-weight: 700; color: white; margin-bottom: 1.5rem; line-height: 1.3; font-family: 'Cormorant Garamond', serif;">
                {{ $publication->title }}
            </h1>
            @if($publication->authors && is_array($publication->authors))
            <div style="display: flex; align-items: center; gap: 0.75rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem; flex-wrap: wrap;">
                <i class="fas fa-user-edit" style="font-size: 1.1rem;"></i>
                <span style="font-size: 1.1rem;">
                    @foreach($publication->authors as $index => $author)
                        {{ $author['name'] ?? $author }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </span>
            </div>
            @elseif($publication->submitter || $publication->primaryAuthor)
            <div style="display: flex; align-items: center; gap: 0.75rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 1rem;">
                <i class="fas fa-user-edit" style="font-size: 1.1rem;"></i>
                <span style="font-size: 1.1rem;">
                    {{ $publication->submitter->name ?? '' }}
                    @if($publication->primaryAuthor && $publication->submitter)
                        , {{ $publication->primaryAuthor->name }}
                    @elseif($publication->primaryAuthor)
                        {{ $publication->primaryAuthor->name }}
                    @endif
                </span>
            </div>
            @endif
            <div style="display: flex; align-items: center; gap: 1.5rem; color: rgba(255, 255, 255, 0.8); font-size: 0.95rem; flex-wrap: wrap;">
                @if($publication->publication_year)
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="far fa-calendar"></i>
                    <span>Published: {{ $publication->publication_year }}</span>
                </div>
                @endif
                @if($publication->published_at)
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $publication->published_at->format('F d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Publication Detail Content -->
<section class="publication-detail-content" style="padding: 3rem 0; background: var(--light-color); min-height: 60vh;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <!-- Main Content Card -->
            <div style="background: white; border-radius: 20px; padding: 3rem; box-shadow: var(--shadow-lg); margin-bottom: 2rem;">
                <!-- Abstract Section -->
                @if($publication->abstract)
                <div style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary-color); font-family: 'Cormorant Garamond', serif; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-file-alt" style="color: var(--accent-color);"></i>
                        Abstract
                    </h2>
                    <p style="color: var(--text-light); line-height: 1.9; font-size: 1.05rem; text-align: justify;">
                        {{ $publication->abstract }}
                    </p>
                </div>
                @endif

                <!-- Publication Details Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; padding: 2rem; background: var(--light-color); border-radius: 15px; border: 1px solid var(--border-light);">
                    @if($publication->journal_name)
                    <div style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-book" style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <strong style="color: var(--primary-color); font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">Journal</strong>
                        </div>
                        <p style="color: var(--text-light); margin: 0; font-size: 1rem; line-height: 1.6;">{{ $publication->journal_name }}</p>
                        @if($publication->journal_category)
                        <span style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: var(--accent-color); color: var(--primary-color); border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                            {{ $publication->journal_category }}
                        </span>
                        @endif
                        @if($publication->quartile)
                        <span style="display: inline-block; margin-top: 0.5rem; margin-left: 0.5rem; padding: 0.25rem 0.75rem; background: var(--primary-color); color: white; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                            Q{{ $publication->quartile }}
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
                        <span style="display: inline-block; padding: 0.5rem 1rem; background: {{ $publication->status === 'approved' ? '#22c55e' : ($publication->status === 'pending' ? '#eab308' : '#ef4444') }}; color: white; border-radius: 20px; font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">
                            {{ ucfirst($publication->status) }}
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Authors Section -->
                @if($publication->authors && is_array($publication->authors) && count($publication->authors) > 0)
                <div style="margin-bottom: 3rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary-color); font-family: 'Cormorant Garamond', serif; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fas fa-users" style="color: var(--accent-color);"></i>
                        Authors
                    </h2>
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                        @foreach($publication->authors as $author)
                        <div style="background: linear-gradient(135deg, var(--light-color) 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-radius: 15px; border: 1px solid var(--border-light); display: flex; align-items: center; gap: 0.75rem; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                            <i class="fas fa-user-circle" style="color: var(--accent-color); font-size: 1.5rem;"></i>
                            <span style="color: var(--text-color); font-weight: 500; font-size: 1rem;">
                                {{ $author['name'] ?? $author }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Submission Info -->
                @if($publication->submitter)
                <div style="padding: 1.5rem; background: var(--light-color); border-radius: 15px; border-left: 4px solid var(--accent-color); margin-bottom: 2rem;">
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

                <!-- Action Buttons -->
                @if($publication->published_link || $publication->proceedings_link)
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; padding-top: 2rem; border-top: 2px solid var(--border-light);">
                    @if($publication->published_link)
                    <a href="{{ $publication->published_link }}" target="_blank" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; background: var(--primary-color); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: all 0.3s; box-shadow: var(--shadow-sm);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform=''; this.style.boxShadow='var(--shadow-sm)';">
                        <i class="fas fa-external-link-alt"></i>
                        View Publication
                    </a>
                    @endif
                    @if($publication->proceedings_link)
                    <a href="{{ $publication->proceedings_link }}" target="_blank" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; background: transparent; color: var(--primary-color); text-decoration: none; border: 2px solid var(--primary-color); border-radius: 10px; font-weight: 600; transition: all 0.3s;" onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';" onmouseout="this.style.background='transparent'; this.style.color='var(--primary-color)';">
                        <i class="fas fa-file-pdf"></i>
                        View Proceedings
                    </a>
                    @endif
                </div>
                @endif
            </div>

            <!-- Additional Info Card -->
            @if($publication->college || $publication->department || $publication->points_allocated)
            <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-lg);">
                <h3 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary-color); font-family: 'Cormorant Garamond', serif;">
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
            </div>
            @endif
        </div>
    </div>
</section>

@push('styles')
<style>
    .publication-detail-hero a:hover {
        opacity: 1;
    }
    
    @media (max-width: 768px) {
        .publication-detail-hero h1 {
            font-size: 1.75rem !important;
        }
        
        .publication-detail-content {
            padding: 2rem 0 !important;
        }
        
        .publication-detail-content > .container > div {
            padding: 2rem !important;
        }
    }
</style>
@endpush
@endsection
