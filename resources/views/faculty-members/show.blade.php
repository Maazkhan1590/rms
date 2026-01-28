@extends('layouts.public')

@section('title', $user->name . ' - Faculty Member | Academic Research Portal')

@section('content')
<!-- Faculty Member Profile Header -->
<header class="page-header">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 2rem;">
            <div class="member-avatar-large" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; font-weight: 700; flex-shrink: 0;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1 style="margin-bottom: 0.5rem;">{{ $user->name }}</h1>
            @if($user->college)
                <p style="color: var(--text-secondary); font-size: 1.125rem; margin-bottom: 0.25rem;">{{ $user->college->name }}</p>
            @endif
            @if($user->department)
                <p style="color: var(--text-secondary); font-size: 1rem;">{{ $user->department->name }}</p>
            @endif
            @if($user->email)
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 0.5rem;">
                    <i class="fas fa-envelope"></i> {{ $user->email }}
                </p>
            @endif
        </div>
    </div>
</header>

<!-- Publications Section -->
<section style="padding: 3rem 0;">
    <div class="container">
        <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 2rem; color: var(--text-color);">
            Submitted Papers ({{ $publications->total() }})
        </h2>

        @if($publications->count() > 0)
        <div class="publications-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($publications as $publication)
            <div class="publication-item" style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: box-shadow 0.3s ease;">
                <div style="display: flex; justify-content: space-between; align-items: start; gap: 2rem;">
                    <div style="flex: 1;">
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-color);">
                            <a href="{{ route('publications.show', $publication->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $publication->title }}
                            </a>
                        </h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                            <span><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) }}</span>
                            @if($publication->publication_year)
                                <span><strong>Year:</strong> {{ $publication->publication_year }}</span>
                            @endif
                            @if($publication->journal_name)
                                <span><strong>Journal:</strong> {{ $publication->journal_name }}</span>
                            @endif
                            @if($publication->conference_name)
                                <span><strong>Conference:</strong> {{ $publication->conference_name }}</span>
                            @endif
                        </div>
                        @if($publication->abstract)
                            <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 1rem;">
                                {{ Str::limit(strip_tags($publication->abstract), 200) }}
                            </p>
                        @endif
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            @if($publication->doi)
                                <a href="https://doi.org/{{ $publication->doi }}" target="_blank" style="color: var(--primary-color); text-decoration: none; font-size: 0.875rem;">
                                    <i class="fas fa-external-link-alt"></i> DOI
                                </a>
                            @endif
                            @if($publication->published_link)
                                <a href="{{ $publication->published_link }}" target="_blank" style="color: var(--primary-color); text-decoration: none; font-size: 0.875rem;">
                                    <i class="fas fa-link"></i> Published Link
                                </a>
                            @endif
                            @if($publication->evidenceFiles && $publication->evidenceFiles->count() > 0)
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">
                                    <i class="fas fa-paperclip"></i> {{ $publication->evidenceFiles->count() }} Evidence File(s)
                                </span>
                            @endif
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $publication->status === 'approved' ? '#10b981' : ($publication->status === 'submitted' ? '#3b82f6' : '#6b7280') }}; color: white;">
                            {{ ucfirst($publication->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top: 3rem; display: flex; justify-content: center;">
            {{ $publications->links() }}
        </div>
        @else
        <div style="text-align: center; padding: 4rem 2rem;">
            <p style="font-size: 1.125rem; color: var(--text-secondary);">No publications found for this faculty member.</p>
        </div>
        @endif
    </div>
</section>
@endsection
