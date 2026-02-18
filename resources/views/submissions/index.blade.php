@extends('layouts.public')

@section('title', 'What would you like to submit? | Academic Research Portal')

@section('content')
<style>
    .submissions-container {
        max-width: 900px;
        width: 100%;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
        z-index: 1;
    }
    .submit-box {
        background: white;
        border-radius: 16px;
        padding: 3rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .submissions-header {
        text-align: center;
        margin-bottom: 2rem;
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
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .submission-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (min-width: 1024px) {
        .submission-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .submission-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
        text-align: center;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 140px;
    }
    .submission-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
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
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
        margin-top: 0.75rem;
    }
    .submission-card p {
        display: none;
    }
    .submission-card .btn {
        display: none;
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

<section class="auth-section">
    <div class="submissions-container">
        <div class="submit-box">
            <div class="submissions-header">
                <h1 style="color: #10b981; font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Submit</h1>
                <p style="font-size: 1rem; color: #111827; margin-bottom: 0.5rem; font-weight: 500;">Share your research work with the academic community</p>
                <p style="font-size: 0.95rem; color: #6b7280; margin-top: 1rem;">What would you like to submit?</p>
            </div>

            <!-- All Submission Types -->
            <div class="submission-grid">
                <a href="{{ route('publications.create') }}" class="submission-card">
                    <div class="submission-card-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Publication</h3>
                </a>

                <a href="{{ route('grants.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Grant</h3>
                </a>

                <a href="{{ route('rtn-submissions.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>RTN</h3>
                </a>

                <a href="{{ route('bonus-recognitions.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Bonus Recognition</h3>
                </a>

                <a href="{{ route('partnerships.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Partnership & MOU</h3>
                </a>

                <a href="{{ route('commercializations.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fce7f3; color: #be185d;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Commercialization</h3>
                </a>

                <a href="{{ route('consultancies.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Consultancy & KT</h3>
                </a>

                <a href="{{ route('awards.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Award</h3>
                </a>

                <a href="{{ route('research-investments.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h3>Research Investment</h3>
                </a>

                <a href="{{ route('conference-activities.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <h3>Conference Activity</h3>
                </a>

                <a href="{{ route('supervision-exams.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Supervision & Exam</h3>
                </a>

                <a href="{{ route('editorial-appointments.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3>Editorial Appointment</h3>
                </a>

                <a href="{{ route('student-involvements.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fce7f3; color: #be185d;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Student Involvement</h3>
                </a>

                <a href="{{ route('research-fellows.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Research Fellow</h3>
                </a>

                <a href="{{ route('sdg-contributions.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>SDG Contribution</h3>
                </a>

                <a href="{{ route('internal-fundings.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #f0fdf4; color: #16a34a;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h3>Internal Funding</h3>
                </a>

                <a href="{{ route('block-fundings.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #eff6ff; color: #2563eb;">
                        <i class="fas fa-cube"></i>
                    </div>
                    <h3>Block Funding</h3>
                </a>

                <a href="{{ route('rtn-course-details.create') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>RTN Course Detail</h3>
                </a>

                <a href="{{ route('adjunct-professors.propose') }}" class="submission-card">
                    <div class="submission-card-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Adjunct Professor</h3>
                </a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
// All submission types shown directly - no tabs needed
</script>
@endpush
@endsection
