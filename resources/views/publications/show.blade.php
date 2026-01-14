@extends('layouts.base')

@section('title', $publication->title . ' - RMS')

@section('content')
<div style="min-height: 100vh; background: var(--bg-secondary); padding-top: 100px;">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <a href="{{ route('publications.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-primary); text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Publications
            </a>
            @auth
            @if(auth()->user()->hasRole('Student'))
            <a href="{{ route('publications.create') }}" class="btn btn-primary" style="text-decoration: none; display: inline-block;">
                <i class="fas fa-plus-circle"></i> Submit New Publication
            </a>
            @endif
            @endauth
        </div>

        <div style="background: white; border-radius: 20px; padding: 3rem; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; display: inline-block; margin-bottom: 1rem;">
                        {{ ucfirst($publication->publication_type ?? 'Publication') }}
                    </span>
                    @if($publication->publication_year)
                    <p style="color: var(--text-secondary); margin: 0.5rem 0;">Published: {{ $publication->publication_year }}</p>
                    @endif
                </div>
            </div>

            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--text-primary); line-height: 1.3;">
                {{ $publication->title }}
            </h1>

            @if($publication->abstract)
            <div style="margin-bottom: 2rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">Abstract</h2>
                <p style="color: var(--text-secondary); line-height: 1.8; font-size: 1rem;">
                    {{ $publication->abstract }}
                </p>
            </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-secondary); border-radius: 15px;">
                @if($publication->journal_name)
                <div>
                    <strong style="color: var(--text-primary);">Journal:</strong>
                    <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0;">{{ $publication->journal_name }}</p>
                </div>
                @endif

                @if($publication->conference_name)
                <div>
                    <strong style="color: var(--text-primary);">Conference:</strong>
                    <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0;">{{ $publication->conference_name }}</p>
                </div>
                @endif

                @if($publication->publisher)
                <div>
                    <strong style="color: var(--text-primary);">Publisher:</strong>
                    <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0;">{{ $publication->publisher }}</p>
                </div>
                @endif

                @if($publication->doi)
                <div>
                    <strong style="color: var(--text-primary);">DOI:</strong>
                    <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                        <a href="https://doi.org/{{ $publication->doi }}" target="_blank" style="color: var(--color-primary);">{{ $publication->doi }}</a>
                    </p>
                </div>
                @endif

                @if($publication->isbn)
                <div>
                    <strong style="color: var(--text-primary);">ISBN:</strong>
                    <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0;">{{ $publication->isbn }}</p>
                </div>
                @endif
            </div>

            @if($publication->authors && is_array($publication->authors))
            <div style="margin-bottom: 2rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-primary);">Authors</h2>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                    @foreach($publication->authors as $author)
                    <span style="background: var(--color-primary-50); color: var(--color-primary-700); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem;">
                        {{ $author['name'] ?? $author }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($publication->submitter)
            <div style="margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-secondary); border-radius: 15px;">
                <strong style="color: var(--text-primary);">Submitted by:</strong>
                <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0;">{{ $publication->submitter->name }}</p>
            </div>
            @endif

            @if($publication->published_link || $publication->proceedings_link)
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                @if($publication->published_link)
                <a href="{{ $publication->published_link }}" target="_blank" class="btn btn-primary" style="text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> View Publication
                </a>
                @endif
                @if($publication->proceedings_link)
                <a href="{{ $publication->proceedings_link }}" target="_blank" class="btn btn-outline" style="text-decoration: none;">
                    <i class="fas fa-file-pdf"></i> View Proceedings
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
