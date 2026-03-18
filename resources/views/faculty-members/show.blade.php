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
                <div style="display: flex; flex-direction: column; gap: .5rem; align-items: flex-end; margin-bottom: .75rem;">
                    <a href="{{ route('faculty-members.cv.view', $user->id) }}" 
                       target="_blank"
                       class="btn btn-view-cv" 
                       style="display: inline-flex; align-items: center; gap: .5rem; padding: .65rem 1.25rem; background: #10b981; color: white; text-decoration: none; border-radius: 8px; font-size: .9rem; font-weight: 600; transition: background .2s ease; min-width: 180px; justify-content: center;"
                       onmouseover="this.style.background='#059669'"
                       onmouseout="this.style.background='#10b981'">
                        <i class="fas fa-eye"></i>
                        <span>View CV (PDF)</span>
                    </a>
                    <a href="{{ route('faculty-members.cv.download', $user->id) }}" 
                       class="btn btn-download-cv" 
                       style="display: inline-flex; align-items: center; gap: .5rem; padding: .65rem 1.25rem; background: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-size: .9rem; font-weight: 600; transition: background .2s ease; min-width: 180px; justify-content: center;"
                       onmouseover="this.style.background='#1e40af'"
                       onmouseout="this.style.background='#2563eb'">
                        <i class="fas fa-download"></i>
                        <span>Download CV</span>
                    </a>
                    @auth
                        @if(auth()->id() == $user->id)
                        <a href="{{ route('profile.password.edit') }}" 
                           class="btn btn-edit-profile" 
                           style="display: inline-flex; align-items: center; gap: .5rem; padding: .65rem 1.25rem; background: #7c3aed; color: white; text-decoration: none; border-radius: 8px; font-size: .9rem; font-weight: 600; transition: background .2s ease; min-width: 180px; justify-content: center; margin-top: .5rem;"
                           onmouseover="this.style.background='#6d28d9'"
                           onmouseout="this.style.background='#7c3aed'">
                            <i class="fas fa-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                        @endif
                    @endauth
                </div>
                <div style="margin-bottom:.4rem; font-weight:500; color:#6b7280;">Detailed Information</div>
                <div>Profile generated from RMS data</div>
            </div>
        </div>

        <hr style="border:none; border-top:1px solid #e5e7eb; margin:0 0 1.25rem;">

        <!-- Profile Tabs: All Modules -->
        <div class="profile-tabs" style="margin-top:.5rem; overflow-x: auto; flex-wrap: wrap;">
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
            <button type="button" class="profile-tab" data-tab="partnerships">
                <span>Partnerships</span>
                <span class="profile-tab-count">{{ isset($partnerships) ? $partnerships->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="commercializations">
                <span>Commercializations</span>
                <span class="profile-tab-count">{{ isset($commercializations) ? $commercializations->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="consultancies">
                <span>Consultancies</span>
                <span class="profile-tab-count">{{ isset($consultancies) ? $consultancies->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="awards">
                <span>Awards</span>
                <span class="profile-tab-count">{{ isset($awards) ? $awards->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="investments">
                <span>Investments</span>
                <span class="profile-tab-count">{{ isset($researchInvestments) ? $researchInvestments->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="conferences">
                <span>Conferences</span>
                <span class="profile-tab-count">{{ isset($conferenceActivities) ? $conferenceActivities->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="supervision">
                <span>Supervision</span>
                <span class="profile-tab-count">{{ isset($supervisionExams) ? $supervisionExams->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="editorial">
                <span>Editorial</span>
                <span class="profile-tab-count">{{ isset($editorialAppointments) ? $editorialAppointments->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="students">
                <span>Students</span>
                <span class="profile-tab-count">{{ isset($studentInvolvements) ? $studentInvolvements->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="fellows">
                <span>Fellows</span>
                <span class="profile-tab-count">{{ isset($researchFellows) ? $researchFellows->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="sdg">
                <span>SDG</span>
                <span class="profile-tab-count">{{ isset($sdgContributions) ? $sdgContributions->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="internal-funding">
                <span>Internal Funding</span>
                <span class="profile-tab-count">{{ isset($internalFundings) ? $internalFundings->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="block-funding">
                <span>Block Funding</span>
                <span class="profile-tab-count">{{ isset($blockFundings) ? $blockFundings->count() : 0 }}</span>
            </button>
            <button type="button" class="profile-tab" data-tab="rtn-courses">
                <span>RTN Courses</span>
                <span class="profile-tab-count">{{ isset($rtnCourseDetails) ? $rtnCourseDetails->count() : 0 }}</span>
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
                            <!-- Draft Submit Banner -->
                            @auth
                                @if($publication->status === 'draft' && ($publication->submitted_by === auth()->id() || $publication->primary_author_id === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This publication needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('publications.submit', $publication->id) }}" method="POST" style="margin: 0;" class="submit-publication-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            
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
                                    @php
                                        $statusColors = [
                                            'approved' => '#10b981',
                                            'pending' => '#eab308',
                                            'pending_coordinator' => '#6b7280',
                                            'pending_dean' => '#6b7280',
                                            'submitted' => '#3b82f6',
                                            'rejected' => '#ef4444',
                                            'draft' => '#6b7280',
                                        ];
                                        $bgColor = $statusColors[$publication->status] ?? '#6b7280';
                                    @endphp
                                    <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
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
                            <!-- Draft Submit Banner -->
                            @auth
                                @if($grant->status === 'draft' && $grant->submitted_by === auth()->id())
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This grant needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('grants.submit', $grant->id) }}" method="POST" style="margin: 0;" class="submit-grant-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            
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
                                    @php
                                        $statusColors = [
                                            'approved' => '#10b981',
                                            'pending' => '#eab308',
                                            'pending_coordinator' => '#6b7280',
                                            'pending_dean' => '#6b7280',
                                            'submitted' => '#3b82f6',
                                            'rejected' => '#ef4444',
                                            'draft' => '#6b7280',
                                        ];
                                        $bgColor = $statusColors[$grant->status ?? 'draft'] ?? '#6b7280';
                                    @endphp
                                    <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
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
                            <!-- Draft Submit Banner -->
                            @auth
                                @if($rtn->status === 'draft' && $rtn->user_id === auth()->id())
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This RTN submission needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('rtn-submissions.submit', $rtn->id) }}" method="POST" style="margin: 0;" class="submit-rtn-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            
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
                            <!-- Draft Submit Banner -->
                            @auth
                                @if($bonus->status === 'draft' && $bonus->user_id === auth()->id())
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This recognition needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('bonus-recognitions.submit', $bonus->id) }}" method="POST" style="margin: 0;" class="submit-bonus-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            
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
                                    @php
                                        $statusColors = [
                                            'approved' => '#10b981',
                                            'pending' => '#eab308',
                                            'pending_coordinator' => '#6b7280',
                                            'pending_dean' => '#6b7280',
                                            'submitted' => '#3b82f6',
                                            'rejected' => '#ef4444',
                                            'draft' => '#6b7280',
                                        ];
                                        $bgColor = $statusColors[$bonus->status ?? 'draft'] ?? '#6b7280';
                                    @endphp
                                    <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
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

        <!-- Partnerships Tab -->
        @if(isset($partnerships))
        <div class="profile-tab-content" data-tab="partnerships" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Partnerships & MOUs ({{ $partnerships->count() }})
            </h2>
            @if($partnerships->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($partnerships as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This partnership needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('partnerships.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-partnership-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('partnerships.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->partner_organization ?? $item->partner_name ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Type:</strong> {{ $item->type ?? $item->mou_type ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No partnerships found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Commercializations Tab -->
        @if(isset($commercializations))
        <div class="profile-tab-content" data-tab="commercializations" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Commercializations ({{ $commercializations->count() }})
            </h2>
            @if($commercializations->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($commercializations as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This commercialization needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('commercializations.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-commercialization-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('commercializations.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->product_service_name ?? $item->title ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Type:</strong> {{ $item->type ?? $item->commercialization_type ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No commercializations found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Consultancies Tab -->
        @if(isset($consultancies))
        <div class="profile-tab-content" data-tab="consultancies" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Consultancies & KT ({{ $consultancies->count() }})
            </h2>
            @if($consultancies->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($consultancies as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This consultancy needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('consultancies.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-consultancy-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('consultancies.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->project_consultancy_name ?? $item->title ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Type:</strong> {{ $item->income_type ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                                @if($item->amount_omr)
                                    <span><strong>Amount:</strong> OMR {{ number_format($item->amount_omr, 2) }}</span>
                                @endif
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No consultancies found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Awards Tab -->
        @if(isset($awards))
        <div class="profile-tab-content" data-tab="awards" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Awards ({{ $awards->count() }})
            </h2>
            @if($awards->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($awards as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This award needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('awards.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-award-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('awards.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->award_name ?? $item->title ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Organization:</strong> {{ $item->awarding_organization ?? $item->organization ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No awards found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Research Investments Tab -->
        @if(isset($researchInvestments))
        <div class="profile-tab-content" data-tab="investments" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Research Investments ({{ $researchInvestments->count() }})
            </h2>
            @if($researchInvestments->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($researchInvestments as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This research investment needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('research-investments.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-investment-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('research-investments.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->item ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Category:</strong> {{ ucfirst(str_replace('_', ' ', $item->category ?? 'N/A')) }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                                @if($item->amount_omr)
                                    <span><strong>Amount:</strong> OMR {{ number_format($item->amount_omr, 2) }}</span>
                                @endif
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No research investments found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Conference Activities Tab -->
        @if(isset($conferenceActivities))
        <div class="profile-tab-content" data-tab="conferences" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Conference Activities ({{ $conferenceActivities->count() }})
            </h2>
            @if($conferenceActivities->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($conferenceActivities as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This conference activity needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('conference-activities.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-conference-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('conference-activities.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->conference ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Activity:</strong> {{ ucfirst(str_replace('_', ' ', $item->activity_type ?? 'N/A')) }}</span>
                                <span><strong>Country:</strong> {{ $item->country ?? 'N/A' }}</span>
                                @if($item->date)
                                    <span><strong>Date:</strong> {{ \Carbon\Carbon::parse($item->date)->format('M Y') }}</span>
                                @endif
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No conference activities found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Supervision & Exams Tab -->
        @if(isset($supervisionExams))
        <div class="profile-tab-content" data-tab="supervision" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Supervision & Examinations ({{ $supervisionExams->count() }})
            </h2>
            @if($supervisionExams->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($supervisionExams as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This supervision entry needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('supervision-exams.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-supervision-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('supervision-exams.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->student_name ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $item->role ?? 'N/A')) }}</span>
                                <span><strong>Degree:</strong> {{ $item->degree ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->academic_year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No supervision records found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Editorial Appointments Tab -->
        @if(isset($editorialAppointments))
        <div class="profile-tab-content" data-tab="editorial" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Editorial Appointments ({{ $editorialAppointments->count() }})
            </h2>
            @if($editorialAppointments->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($editorialAppointments as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This editorial appointment needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('editorial-appointments.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-editorial-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('editorial-appointments.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->journal_conference ?? 'N/A' }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Role:</strong> {{ $item->role ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No editorial appointments found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Student Involvements Tab -->
        @if(isset($studentInvolvements))
        <div class="profile-tab-content" data-tab="students" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Student Involvements ({{ $studentInvolvements->count() }})
            </h2>
            @if($studentInvolvements->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($studentInvolvements as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This student involvement needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('student-involvements.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-student-involvement-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('student-involvements.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ ucfirst(str_replace('_', ' ', $item->category ?? 'N/A')) }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Count:</strong> {{ $item->count ?? 'N/A' }}</span>
                                <span><strong>Academic Year:</strong> {{ $item->academic_year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No student involvements found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Research Fellows Tab -->
        @if(isset($researchFellows))
        <div class="profile-tab-content" data-tab="fellows" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Research Fellows ({{ $researchFellows->count() }})
            </h2>
            @if($researchFellows->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($researchFellows as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This research fellow entry needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('research-fellows.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-research-fellow-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('research-fellows.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ Str::limit($item->publication_title ?? 'N/A', 60) }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Journal:</strong> {{ Str::limit($item->journal ?? 'N/A', 40) }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No research fellows found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- SDG Contributions Tab -->
        @if(isset($sdgContributions))
        <div class="profile-tab-content" data-tab="sdg" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                SDG Contributions ({{ $sdgContributions->count() }})
            </h2>
            @if($sdgContributions->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($sdgContributions as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This SDG contribution needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('sdg-contributions.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-sdg-contribution-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('sdg-contributions.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ Str::limit($item->title ?? 'N/A', 60) }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>SDG:</strong> SDG {{ $item->sdg ?? 'N/A' }}</span>
                                <span><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $item->type ?? 'N/A')) }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No SDG contributions found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Internal Fundings Tab -->
        @if(isset($internalFundings))
        <div class="profile-tab-content" data-tab="internal-funding" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Internal Fundings ({{ $internalFundings->count() }})
            </h2>
            @if($internalFundings->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($internalFundings as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This internal funding needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('internal-fundings.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-internal-funding-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('internal-fundings.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ Str::limit($item->project_title ?? 'N/A', 60) }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Source:</strong> {{ $item->funding_source ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                                @if($item->amount_omr)
                                    <span><strong>Amount:</strong> OMR {{ number_format($item->amount_omr, 2) }}</span>
                                @endif
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No internal fundings found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- Block Fundings Tab -->
        @if(isset($blockFundings))
        <div class="profile-tab-content" data-tab="block-funding" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                Block Fundings ({{ $blockFundings->count() }})
            </h2>
            @if($blockFundings->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($blockFundings as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This block funding needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('block-fundings.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-block-funding-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('block-fundings.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ Str::limit($item->project_title ?? 'N/A', 60) }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>Source:</strong> {{ $item->funding_source ?? 'N/A' }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                                @if($item->amount_omr)
                                    <span><strong>Amount:</strong> OMR {{ number_format($item->amount_omr, 2) }}</span>
                                @endif
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No block fundings found.</p>
                </div>
            @endif
        </div>
        @endif

        <!-- RTN Course Details Tab -->
        @if(isset($rtnCourseDetails))
        <div class="profile-tab-content" data-tab="rtn-courses" style="display:none;">
            <h2 style="font-size: 1.75rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-color);">
                RTN Course Details ({{ $rtnCourseDetails->count() }})
            </h2>
            @if($rtnCourseDetails->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($rtnCourseDetails as $item)
                        <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @php
                                $itemStatus = $item->status ?? $item->workflow_status ?? 'draft';
                                $statusColors = [
                                    'approved' => '#10b981',
                                    'pending' => '#eab308',
                                    'pending_coordinator' => '#6b7280',
                                    'pending_dean' => '#6b7280',
                                    'submitted' => '#3b82f6',
                                    'rejected' => '#ef4444',
                                    'draft' => '#6b7280',
                                ];
                                $bgColor = $statusColors[$itemStatus] ?? '#6b7280';
                            @endphp
                            @auth
                                @if($itemStatus === 'draft' && (($item->submitted_by ?? null) === auth()->id() || ($item->user_id ?? null) === auth()->id()))
                                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 0.875rem 1.25rem; border-radius: 6px; margin-bottom: 1.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                                        <div style="flex: 1;">
                                            <p style="color: #92400e; font-size: 0.875rem; margin: 0; font-weight: 500;">
                                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This RTN course detail needs to be submitted for approval.
                                            </p>
                                        </div>
                                        <form action="{{ route('rtn-course-details.submit', $item->id) }}" method="POST" style="margin: 0;" class="submit-rtn-course-form">
                                            @csrf
                                            <button type="submit" style="padding: 0.5rem 1.25rem; background: #f59e0b; border: none; border-radius: 6px; color: white; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                                <i class="fas fa-paper-plane"></i> Submit for Approval
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            @endauth
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <a href="{{ route('rtn-course-details.show', $item->id) }}" style="color: inherit; text-decoration: none;">{{ $item->course_code ?? 'N/A' }} - {{ Str::limit($item->course_name ?? 'N/A', 50) }}</a>
                            </h3>
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                                <span><strong>RTN Type:</strong> {{ str_replace('_', ' ', strtoupper($item->rtn_type ?? 'N/A')) }}</span>
                                <span><strong>Year:</strong> {{ $item->year ?? 'N/A' }}</span>
                            </div>
                            <div style="margin-top: 1rem; text-align: right;">
                                <span class="badge" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; font-weight: 600; background: {{ $bgColor }}; color: white;">
                                    {{ ucfirst($itemStatus) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 4rem 2rem;">
                    <p style="font-size: 1.125rem; color: var(--text-secondary);">No RTN course details found.</p>
                </div>
            @endif
        </div>
        @endif
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
