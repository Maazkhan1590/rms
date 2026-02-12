@extends('layouts.public')

@section('title', $user->name . ' - Faculty Member | Academic Research Portal')

@push('styles')
<style>
    .researcher-profile-body {
        padding: 6.75rem 0 3.5rem;
        background: #f3f4f6;
    }
    .researcher-profile-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(15,23,42,0.10);
        padding: 2rem 2.25rem;
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
<!-- Profile wrapper (single card like reference site) -->
<section class="researcher-profile-body">
    <div class="container researcher-profile-card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem;">
            <div style="display:flex; gap:1.5rem; align-items:center;">
                <div class="member-avatar-large" style="width: 90px; height: 90px; border-radius: 12px; background: #e5e7eb; display:flex; align-items:center; justify-content:center; font-size:2.2rem; color:#4b5563; font-weight:700; flex-shrink:0;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 style="margin-bottom: .25rem; font-size:1.6rem; font-weight:600; color:#111827;">{{ $user->name }}</h1>
                    @if($user->college)
                        <p style="color:#4b5563; font-size:.95rem; margin-bottom:.1rem;">{{ $user->college->name }}</p>
                    @endif
                    @if($user->department)
                        <p style="color:#6b7280; font-size:.9rem; margin-bottom:.1rem;">{{ $user->department->name }}</p>
                    @endif
                    @if($user->designation)
                        <p style="color:#6b7280; font-size:.9rem; margin-top:.25rem;">{{ $user->designation }}</p>
                    @endif
                    @if($user->email)
                        <p style="color:#6b7280; font-size:.85rem; margin-top:.4rem;">
                            <i class="fas fa-envelope"></i> {{ $user->email }}
                        </p>
                    @endif
                    @if($user->orcid || $user->google_scholar || $user->research_gate)
                        <div style="margin-top: .5rem; display:flex; flex-wrap:wrap; gap:.75rem; font-size:.8rem;">
                            @if($user->orcid)
                                <a href="{{ $user->orcid }}" target="_blank" style="color:#4b5563; text-decoration:none;">
                                    <i class="fab fa-orcid"></i> ORCID
                                </a>
                            @endif
                            @if($user->google_scholar)
                                <a href="{{ $user->google_scholar }}" target="_blank" style="color:#4b5563; text-decoration:none;">
                                    <i class="fas fa-graduation-cap"></i> Google Scholar
                                </a>
                            @endif
                            @if($user->research_gate)
                                <a href="{{ $user->research_gate }}" target="_blank" style="color:#4b5563; text-decoration:none;">
                                    <i class="fas fa-project-diagram"></i> ResearchGate
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div style="text-align:right; font-size:.8rem; color:#9ca3af;">
                <a href="{{ route('faculty-members.cv', $user->id) }}" 
                   class="btn btn-primary" 
                   style="display: inline-flex; align-items: center; gap: .5rem; padding: .65rem 1.25rem; background: #2c5aa0; color: white; text-decoration: none; border-radius: 8px; font-size: .9rem; font-weight: 600; margin-bottom: .75rem; transition: background .2s ease;"
                   onmouseover="this.style.background='#1e3a8a'"
                   onmouseout="this.style.background='#2c5aa0'">
                    <i class="fas fa-download"></i>
                    <span>Download CV (PDF)</span>
                </a>
                <div style="margin-bottom:.4rem; font-weight:500; color:#6b7280;">Detailed Information</div>
                <div>Profile generated from RMS data</div>
            </div>
        </div>

        <hr style="border:none; border-top:1px solid #e5e7eb; margin:0 0 1.25rem;">

        <!-- Profile Tabs: Publications / Grants / RTN / Recognitions -->
        <div class="profile-tabs" style="margin-top:.5rem;">
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
