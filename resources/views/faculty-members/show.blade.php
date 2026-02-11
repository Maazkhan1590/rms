@extends('layouts.public')

@section('title', $user->name . ' - Faculty Member | Academic Research Portal')

@push('styles')
<style>
    .researcher-profile-header {
        padding: 3rem 0 2rem;
        background: radial-gradient(circle at top left, rgba(37,99,235,0.25), transparent 55%), #020617;
        color: #f9fafb;
    }

    .researcher-profile-header h1 {
        font-size: 2.1rem;
        letter-spacing: .02em;
    }

    .researcher-profile-body {
        padding: 2.5rem 0 3rem;
        background: #f3f4f6;
    }

    .researcher-profile-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 18px 45px rgba(15,23,42,0.12);
        padding: 2rem 2.25rem;
        margin-top: -3rem;
    }

    .profile-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        border-bottom: 1px solid rgba(148,163,184,0.35);
        padding-bottom: .5rem;
        margin-bottom: 1.75rem;
    }

    .profile-tab {
        border: 0;
        background: #f3f4f6;
        color: #4b5563;
        padding: .55rem 1.1rem;
        border-radius: 999px;
        font-size: .9rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        cursor: pointer;
        transition: background .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .profile-tab-count {
        min-width: 1.7rem;
        text-align: center;
        border-radius: 999px;
        background: #e5e7eb;
        font-size: .8rem;
        font-weight: 600;
        padding: .1rem .45rem;
    }

    .profile-tab.active {
        background: #111827;
        color: #f9fafb;
        box-shadow: 0 8px 24px rgba(15,23,42,0.35);
    }

    .profile-tab.active .profile-tab-count {
        background: #22c55e;
        color: #0b1120;
    }

    .profile-tab-content {
        display: none;
    }

    .profile-tab-content.active {
        display: block;
    }

    .publication-item,
    .researcher-card-row {
        border-radius: 10px;
        border: 1px solid rgba(226,232,240,0.9);
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease;
    }

    .publication-item + .publication-item {
        margin-top: 1rem;
    }

    .publication-item:hover,
    .researcher-card-row:hover {
        box-shadow: 0 14px 30px rgba(15,23,42,0.12);
        border-color: rgba(148,163,184,0.75);
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .researcher-profile-card {
            padding: 1.5rem 1.25rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Faculty Member Profile Header -->
<header class="page-header researcher-profile-header">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 2rem;">
            <div class="member-avatar-large" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; font-weight: 700; flex-shrink: 0;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 style="margin-bottom: 0.5rem;">{{ $user->name }}</h1>
                @if($user->designation)
                    <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 0.25rem;">{{ $user->designation }}</p>
                @endif
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

                @if($user->orcid || $user->google_scholar || $user->research_gate)
                    <div style="margin-top: 0.75rem; display:flex; flex-wrap:wrap; gap:0.85rem; font-size:0.85rem;">
                        @if($user->orcid)
                            <a href="{{ $user->orcid }}" target="_blank" style="color:#a5b4fc; text-decoration:none;">
                                <i class="fab fa-orcid"></i> ORCID
                            </a>
                        @endif
                        @if($user->google_scholar)
                            <a href="{{ $user->google_scholar }}" target="_blank" style="color:#bfdbfe; text-decoration:none;">
                                <i class="fas fa-graduation-cap"></i> Google Scholar
                            </a>
                        @endif
                        @if($user->research_gate)
                            <a href="{{ $user->research_gate }}" target="_blank" style="color:#7dd3fc; text-decoration:none;">
                                <i class="fas fa-project-diagram"></i> ResearchGate
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>

<!-- Profile Tabs: Publications / Grants / RTN / Recognitions -->
<section class="researcher-profile-body">
    <div class="container researcher-profile-card">
        @auth
            @if(auth()->id() === $user->id && auth()->user()->hasRole('Faculty'))
                <div style="display:flex; flex-wrap:wrap; gap:.75rem; justify-content:flex-end; margin-bottom:1rem;">
                    <a href="{{ route('publications.create') }}" class="btn btn-sm" style="background:#111827; color:#f9fafb; padding:.45rem .9rem; border-radius:999px; font-size:.85rem; text-decoration:none;">
                        <i class="fas fa-plus-circle"></i> Submit Publication
                    </a>
                    <a href="{{ route('grants.create') }}" class="btn btn-sm" style="background:#0f766e; color:#ecfeff; padding:.45rem .9rem; border-radius:999px; font-size:.85rem; text-decoration:none;">
                        <i class="fas fa-coins"></i> Submit Grant
                    </a>
                    <a href="{{ route('rtn-submissions.create') }}" class="btn btn-sm" style="background:#4338ca; color:#e0e7ff; padding:.45rem .9rem; border-radius:999px; font-size:.85rem; text-decoration:none;">
                        <i class="fas fa-chalkboard-teacher"></i> Submit RTN
                    </a>
                    <a href="{{ route('bonus-recognitions.create') }}" class="btn btn-sm" style="background:#854d0e; color:#fffbeb; padding:.45rem .9rem; border-radius:999px; font-size:.85rem; text-decoration:none;">
                        <i class="fas fa-award"></i> Submit Recognition
                    </a>
                </div>
            @endif
        @endauth
        <div class="profile-tabs">
            <button type="button" class="profile-tab active" data-tab="publications">
                <span>Publications</span>
                <span class="profile-tab-count">{{ $publications->total() }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="grants">
                <span>Grants</span>
                <span class="profile-tab-count">{{ $grants->count() }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="rtn">
                <span>RTN</span>
                <span class="profile-tab-count">{{ $rtnSubmissions->count() }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="bonus">
                <span>Recognitions</span>
                <span class="profile-tab-count">{{ $bonusRecognitions->count() }}</span>
            </button>
        </div>

        <!-- Publications Tab -->
        <div class="profile-tab-content active" data-tab="publications">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Publications ({{ $publications->total() }})
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

        <!-- Grants Tab -->
        <div class="profile-tab-content" data-tab="grants" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Grants ({{ $grants->count() }})
            </h2>

            @if($grants->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($grants as $grant)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-color);">
                                        <a href="{{ route('grants.show', $grant->id) }}" style="color: inherit; text-decoration: none;">
                                            {{ $grant->title }}
                                        </a>
                                    </h3>
                                    <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.875rem; color: var(--text-secondary);">
                                        @if($grant->grant_type)
                                            <span><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $grant->grant_type)) }}</span>
                                        @endif
                                        @if($grant->award_year)
                                            <span><strong>Award Year:</strong> {{ $grant->award_year }}</span>
                                        @endif
                                        @if($grant->amount_omr)
                                            <span><strong>Amount (OMR):</strong> {{ number_format($grant->amount_omr, 2) }}</span>
                                        @endif
                                        @if($grant->sponsor_name)
                                            <span><strong>Sponsor:</strong> {{ $grant->sponsor_name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $grant->status === 'approved' ? '#10b981' : ($grant->status === 'submitted' ? '#3b82f6' : '#6b7280') }}; color: white;">
                                        {{ ucfirst($grant->status ?? 'draft') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No grants found for this faculty member.</p>
                </div>
            @endif
        </div>

        <!-- RTN Tab -->
        <div class="profile-tab-content" data-tab="rtn" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                RTN Submissions ({{ $rtnSubmissions->count() }})
            </h2>

            @if($rtnSubmissions->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($rtnSubmissions as $rtn)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-color);">
                                        <a href="{{ route('rtn-submissions.show', $rtn->id) }}" style="color: inherit; text-decoration: none;">
                                            {{ $rtn->title }}
                                        </a>
                                    </h3>
                                    <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.875rem; color: var(--text-secondary);">
                                        @if($rtn->rtn_type)
                                            <span><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $rtn->rtn_type)) }}</span>
                                        @endif
                                        @if($rtn->year)
                                            <span><strong>Year:</strong> {{ $rtn->year }}</span>
                                        @endif
                                        @if($rtn->points)
                                            <span><strong>Points:</strong> {{ number_format($rtn->points, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $rtn->status === 'approved' ? '#10b981' : ($rtn->status === 'submitted' ? '#3b82f6' : '#6b7280') }}; color: white;">
                                        {{ ucfirst($rtn->status ?? 'draft') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No RTN submissions found for this faculty member.</p>
                </div>
            @endif
        </div>

        <!-- Bonus / Recognitions Tab -->
        <div class="profile-tab-content" data-tab="bonus" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Recognitions & Awards ({{ $bonusRecognitions->count() }})
            </h2>

            @if($bonusRecognitions->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($bonusRecognitions as $bonus)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-color);">
                                        <a href="{{ route('bonus-recognitions.show', $bonus->id) }}" style="color: inherit; text-decoration: none;">
                                            {{ $bonus->title }}
                                        </a>
                                    </h3>
                                    <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.875rem; color: var(--text-secondary);">
                                        @if($bonus->recognition_type)
                                            <span><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $bonus->recognition_type)) }}</span>
                                        @endif
                                        @if($bonus->year)
                                            <span><strong>Year:</strong> {{ $bonus->year }}</span>
                                        @endif
                                        @if($bonus->organization)
                                            <span><strong>Organization:</strong> {{ $bonus->organization }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bonus->status === 'approved' ? '#10b981' : ($bonus->status === 'submitted' ? '#3b82f6' : '#6b7280') }}; color: white;">
                                        {{ ucfirst($bonus->status ?? 'draft') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No recognitions found for this faculty member.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.profile-tab');
        const contents = document.querySelectorAll('.profile-tab-content');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const target = this.getAttribute('data-tab');

                tabs.forEach(function (t) {
                    t.classList.remove('active');
                });

                contents.forEach(function (c) {
                    if (c.getAttribute('data-tab') === target) {
                        c.style.display = 'block';
                        c.classList.add('active');
                    } else {
                        c.style.display = 'none';
                        c.classList.remove('active');
                    }
                });

                this.classList.add('active');
            });
        });
    });
</script>
@endpush
