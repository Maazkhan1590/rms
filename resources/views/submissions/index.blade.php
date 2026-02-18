@extends('layouts.public')

@section('title', 'What would you like to submit? | Academic Research Portal')

@section('content')
<style>
    .submissions-page {
        padding: 6rem 0 4rem;
        background: #f3f4f6;
        min-height: calc(100vh - 200px);
    }
    .submissions-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    .submissions-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .submissions-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
    }
    .submissions-header p {
        font-size: 1.125rem;
        color: #6b7280;
    }
    .submission-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
        overflow-x: auto;
    }
    .submission-tab {
        border: 0;
        background: #f3f4f6;
        color: #4b5563;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .submission-tab:hover {
        background: #e5e7eb;
        color: #111827;
    }
    .submission-tab.active {
        background: #111827;
        color: #ffffff;
    }
    .submission-tab-content {
        display: none;
    }
    .submission-tab-content.active {
        display: block;
    }
    .submission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    .submission-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .submission-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transform: translateY(-2px);
        border-color: #3b82f6;
    }
    .submission-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        background: #eff6ff;
        color: #3b82f6;
    }
    .submission-card h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    .submission-card p {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
        line-height: 1.5;
    }
    .submission-card .btn {
        width: 100%;
        padding: 0.75rem;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    .submission-card .btn:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    @media (max-width: 768px) {
        .submission-grid {
            grid-template-columns: 1fr;
        }
        .submission-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>

<section class="submissions-page">
    <div class="submissions-container">
        <div class="submissions-header">
            <h1>What would you like to submit?</h1>
            <p>Share your research work with the academic community</p>
        </div>

        <!-- Tabs -->
        <div class="submission-tabs">
            <button type="button" class="submission-tab active" data-tab="core">
                <i class="fas fa-star"></i> Core Modules
            </button>
            <button type="button" class="submission-tab" data-tab="research">
                <i class="fas fa-flask"></i> Research Activities
            </button>
            <button type="button" class="submission-tab" data-tab="academic">
                <i class="fas fa-graduation-cap"></i> Academic Activities
            </button>
            <button type="button" class="submission-tab" data-tab="funding">
                <i class="fas fa-dollar-sign"></i> Funding & Impact
            </button>
        </div>

        <!-- Core Modules Tab -->
        <div class="submission-tab-content active" data-tab="core">
            <h2 class="section-title">Core Modules</h2>
            <div class="submission-grid">
                <div class="submission-card">
                    <div class="submission-card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Publication</h3>
                    <p>Submit your research publications, journal articles, conference papers, and other scholarly works.</p>
                    <a href="{{ route('publications.create') }}" class="btn">
                        <i class="fas fa-plus"></i> Submit Publication
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Grant</h3>
                    <p>Submit research grants, funding applications, and sponsored research projects.</p>
                    <a href="{{ route('grants.create') }}" class="btn" style="background: #16a34a;">
                        <i class="fas fa-plus"></i> Submit Grant
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>RTN Submission</h3>
                    <p>Submit Research and Teaching Network activities and contributions.</p>
                    <a href="{{ route('rtn-submissions.create') }}" class="btn" style="background: #d97706;">
                        <i class="fas fa-plus"></i> Submit RTN
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Bonus Recognition</h3>
                    <p>Submit awards, recognitions, honors, and other academic achievements.</p>
                    <a href="{{ route('bonus-recognitions.create') }}" class="btn" style="background: #d97706;">
                        <i class="fas fa-plus"></i> Submit Recognition
                    </a>
                </div>
            </div>
        </div>

        <!-- Research Activities Tab -->
        <div class="submission-tab-content" data-tab="research">
            <h2 class="section-title">Research Activities</h2>
            <div class="submission-grid">
                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Partnership & MOU</h3>
                    <p>Submit partnerships, memorandums of understanding, and collaborative agreements.</p>
                    <a href="{{ route('partnerships.create') }}" class="btn" style="background: #d97706;">
                        <i class="fas fa-plus"></i> Submit Partnership
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fce7f3; color: #be185d;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Commercialization</h3>
                    <p>Submit technology transfers, patents, and commercialization activities.</p>
                    <a href="{{ route('commercializations.create') }}" class="btn" style="background: #be185d;">
                        <i class="fas fa-plus"></i> Submit Commercialization
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Consultancy & KT</h3>
                    <p>Submit consultancy projects and knowledge transfer activities.</p>
                    <a href="{{ route('consultancies.create') }}" class="btn" style="background: #2563eb;">
                        <i class="fas fa-plus"></i> Submit Consultancy
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Award</h3>
                    <p>Submit academic awards, honors, and recognitions received.</p>
                    <a href="{{ route('awards.create') }}" class="btn" style="background: #d97706;">
                        <i class="fas fa-plus"></i> Submit Award
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h3>Research Investment</h3>
                    <p>Submit research equipment, software, training, and other research investments.</p>
                    <a href="{{ route('research-investments.create') }}" class="btn" style="background: #16a34a;">
                        <i class="fas fa-plus"></i> Submit Investment
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <h3>Conference Activity</h3>
                    <p>Submit conference presentations, keynote speeches, and conference participation.</p>
                    <a href="{{ route('conference-activities.create') }}" class="btn" style="background: #d97706;">
                        <i class="fas fa-plus"></i> Submit Activity
                    </a>
                </div>
            </div>
        </div>

        <!-- Academic Activities Tab -->
        <div class="submission-tab-content" data-tab="academic">
            <h2 class="section-title">Academic Activities</h2>
            <div class="submission-grid">
                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Supervision & Exam</h3>
                    <p>Submit student supervision, thesis examination, and academic mentoring activities.</p>
                    <a href="{{ route('supervision-exams.create') }}" class="btn" style="background: #16a34a;">
                        <i class="fas fa-plus"></i> Submit Supervision
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3>Editorial Appointment</h3>
                    <p>Submit editorial board memberships, journal editorships, and review activities.</p>
                    <a href="{{ route('editorial-appointments.create') }}" class="btn" style="background: #2563eb;">
                        <i class="fas fa-plus"></i> Submit Appointment
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fce7f3; color: #be185d;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Student Involvement</h3>
                    <p>Submit student engagement activities, mentoring, and student-related contributions.</p>
                    <a href="{{ route('student-involvements.create') }}" class="btn" style="background: #be185d;">
                        <i class="fas fa-plus"></i> Submit Involvement
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Research Fellow</h3>
                    <p>Submit research fellowship activities and contributions.</p>
                    <a href="{{ route('research-fellows.create') }}" class="btn" style="background: #16a34a;">
                        <i class="fas fa-plus"></i> Submit Fellow
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>RTN Course Detail</h3>
                    <p>Submit RTN course details and course-related research activities.</p>
                    <a href="{{ route('rtn-course-details.create') }}" class="btn" style="background: #d97706;">
                        <i class="fas fa-plus"></i> Submit Course
                    </a>
                </div>
            </div>
        </div>

        <!-- Funding & Impact Tab -->
        <div class="submission-tab-content" data-tab="funding">
            <h2 class="section-title">Funding & Impact</h2>
            <div class="submission-grid">
                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Internal Funding</h3>
                    <p>Submit internal research funding, institutional grants, and internal support.</p>
                    <a href="{{ route('internal-fundings.create') }}" class="btn" style="background: #16a34a;">
                        <i class="fas fa-plus"></i> Submit Funding
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-cube"></i>
                    </div>
                    <h3>Block Funding</h3>
                    <p>Submit block grants, institutional block funding, and allocated research budgets.</p>
                    <a href="{{ route('block-fundings.create') }}" class="btn" style="background: #2563eb;">
                        <i class="fas fa-plus"></i> Submit Funding
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>SDG Contribution</h3>
                    <p>Submit contributions to Sustainable Development Goals and social impact activities.</p>
                    <a href="{{ route('sdg-contributions.create') }}" class="btn" style="background: #16a34a;">
                        <i class="fas fa-plus"></i> Submit Contribution
                    </a>
                </div>

                <div class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Adjunct Professor</h3>
                    <p>Propose adjunct professors and visiting faculty members.</p>
                    <a href="{{ route('adjunct-professors.propose') }}" class="btn" style="background: #d97706;">
                        <i class="fas fa-plus"></i> Propose Professor
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.submission-tab');
    const contents = document.querySelectorAll('.submission-tab-content');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const target = this.getAttribute('data-tab');

            // Remove active class from all tabs and contents
            tabs.forEach(function(t) {
                t.classList.remove('active');
            });
            contents.forEach(function(c) {
                c.classList.remove('active');
            });

            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            const targetContent = document.querySelector(`.submission-tab-content[data-tab="${target}"]`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
});
</script>
@endpush
@endsection
