@extends('layouts.public')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $bonus->title . ' | Bonus Recognition Details - RMS')

@push('styles')
<style>
    .publication-detail-page {
        padding: 6.75rem 0 3.5rem;
        background: #f3f4f6;
    }

    .publication-detail-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 12px 32px rgba(15,23,42,0.10);
        padding: 2.5rem 3rem;
        max-width: 1000px;
        margin: 0 auto;
    }

    .publication-header {
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 1.75rem;
        margin-bottom: 2rem;
    }

    .publication-title {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
        margin-bottom: 1rem;
    }

    .publication-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        color: #6b7280;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    .publication-badges {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }

    .badge-pill {
        padding: 0.4rem 1rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .detail-item {
        padding: 1.25rem;
        background: #f9fafb;
        border-radius: 10px;
        border-left: 3px solid #3b82f6;
    }

    .detail-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .detail-value {
        font-size: 1rem;
        color: #111827;
        font-weight: 500;
    }

    .evidence-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .evidence-table th {
        background: #f9fafb;
        padding: 0.875rem 1rem;
        text-align: left;
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e5e7eb;
    }

    .evidence-table td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
        font-size: 0.9rem;
    }

    .evidence-table tr:hover {
        background: #f9fafb;
    }

    @media (max-width: 768px) {
        .publication-detail-card {
            padding: 1.5rem 1.5rem;
        }
        .publication-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<section class="publication-detail-page">
    <div class="container">
        <div class="publication-detail-card">
            <!-- Back Link -->
            @php
                $backUrl = request()->headers->get('referer');
                $backText = 'Back';
                if ($backUrl) {
                    if (str_contains($backUrl, '/faculty-members/')) {
                        $backText = 'Back to Profile';
                    } elseif (str_contains($backUrl, '/bonus-recognitions')) {
                        $backText = 'Back to Recognitions';
                    } elseif (str_contains($backUrl, '/publications')) {
                        $backText = 'Back to Publications';
                    }
                } else {
                    $backUrl = route('welcome');
                    $backText = 'Back to Home';
                }
            @endphp
            <a href="{{ $backUrl }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; margin-bottom: 1.5rem; font-weight: 500; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> {{ $backText }}
            </a>

            <!-- Draft Submit Banner -->
            @auth
                @if($bonus->status === 'draft' && ($bonus->user_id === auth()->id()))
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div style="flex: 1;">
                            <p style="color: #92400e; font-size: 0.95rem; margin: 0; font-weight: 500;">
                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This recognition needs to be submitted for approval.
                            </p>
                        </div>
                        <form action="{{ route('bonus-recognitions.submit', $bonus->id) }}" method="POST" style="margin: 0;" class="submit-bonus-form">
                            @csrf
                            <button type="submit" style="padding: 0.625rem 1.5rem; background: #f59e0b; border: none; border-radius: 8px; color: white; font-weight: 600; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <!-- Bonus Recognition Header -->
            <div class="publication-header">
                <h1 class="publication-title">{{ $bonus->title ?? 'Untitled Recognition' }}</h1>
                
                @if($bonus->user)
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #374151; font-size: 0.9rem;">Submitted by:</strong>
                    <span style="color: #6b7280; font-size: 0.95rem; margin-left: 0.5rem;">{{ $bonus->user->name }}</span>
                </div>
                @endif

                <div class="publication-meta">
                    @if($bonus->recognition_type)
                        <div><strong>Recognition Type:</strong> {{ ucfirst(str_replace('_', ' ', $bonus->recognition_type)) }}</div>
                    @endif
                    @if($bonus->year)
                        <div><strong>Year:</strong> {{ $bonus->year }}</div>
                    @endif
                    @if($bonus->organization)
                        <div><strong>Organization:</strong> {{ $bonus->organization }}</div>
                    @endif
                    @if($bonus->points)
                        <div><strong>Points:</strong> {{ number_format($bonus->points, 2) }}</div>
                    @endif
                </div>

                <div class="publication-badges">
                    @if($bonus->recognition_type)
                        <span class="badge-pill" style="background: #eff6ff; color: #1d4ed8;">
                            {{ strtoupper(str_replace('_', ' ', $bonus->recognition_type)) }}
                        </span>
                    @endif
                    @if($bonus->status)
                        <span class="badge-pill" style="background: {{ $bonus->status === 'approved' ? '#22c55e' : ($bonus->status === 'submitted' || $bonus->status === 'pending' ? '#eab308' : '#6b7280') }}; color: #fff;">
                            {{ ucfirst($bonus->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Description Section -->
            @if($bonus->role_description || $bonus->description)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-file-alt" style="color: #3b82f6; margin-right: 0.5rem;"></i>Description
                </h2>
                @if($bonus->role_description)
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #374151; font-size: 0.95rem; display: block; margin-bottom: 0.5rem;">Role Description:</strong>
                    <p style="color: #374151; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                        {{ $bonus->role_description }}
                    </p>
                </div>
                @endif
                @if($bonus->description)
                <p style="color: #374151; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                    {{ $bonus->description }}
                </p>
                @endif
            </div>
            @endif

            <!-- Recognition Details Grid -->
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i>Recognition Details
                </h2>
                <div class="detail-grid">
                    @if($bonus->recognition_type)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-tag" style="margin-right: 0.5rem;"></i>Recognition Type</div>
                        <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $bonus->recognition_type)) }}</div>
                    </div>
                    @endif

                    @if($bonus->year)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar" style="margin-right: 0.5rem;"></i>Year</div>
                        <div class="detail-value">{{ $bonus->year }}</div>
                    </div>
                    @endif

                    @if($bonus->submission_year)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-alt" style="margin-right: 0.5rem;"></i>Submission Year</div>
                        <div class="detail-value">{{ $bonus->submission_year }}</div>
                    </div>
                    @endif

                    @if($bonus->organization)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-building" style="margin-right: 0.5rem;"></i>Organization</div>
                        <div class="detail-value">{{ $bonus->organization }}</div>
                    </div>
                    @endif

                    @if($bonus->journal_conference_name)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-book" style="margin-right: 0.5rem;"></i>Journal/Conference</div>
                        <div class="detail-value">{{ $bonus->journal_conference_name }}</div>
                    </div>
                    @endif

                    @if($bonus->event_name)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-day" style="margin-right: 0.5rem;"></i>Event Name</div>
                        <div class="detail-value">{{ $bonus->event_name }}</div>
                    </div>
                    @endif

                    @if($bonus->event_date)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-check" style="margin-right: 0.5rem;"></i>Event Date</div>
                        <div class="detail-value">{{ $bonus->event_date->format('F d, Y') }}</div>
                    </div>
                    @endif

                    @if($bonus->points)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-star" style="margin-right: 0.5rem;"></i>Points</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($bonus->points, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Evidence Files Section -->
            @php
                $evidenceFiles = $bonus->evidenceFiles ?? collect();
                $hasLinkEvidence = !empty($bonus->evidence_link);
                $hasAnyEvidence = $evidenceFiles->count() > 0 || $hasLinkEvidence;
            @endphp
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-paperclip" style="color: #3b82f6; margin-right: 0.5rem;"></i>Evidence & Attachments
                </h2>
                @if($hasAnyEvidence)
                <table class="evidence-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Uploaded By</th>
                            <th>Upload Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evidenceFiles as $file)
                        <tr>
                            <td style="font-weight: 500;">{{ $file->file_name }}</td>
                            <td>
                                @if($file->file_type === 'text/url')
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #dbeafe; color: #1e40af; font-size: 0.8rem; font-weight: 600;">URL</span>
                                @elseif(str_contains($file->file_type, 'image'))
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #d1fae5; color: #065f46; font-size: 0.8rem; font-weight: 600;">Image</span>
                                @elseif(str_contains($file->file_type, 'pdf'))
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #fee2e2; color: #991b1b; font-size: 0.8rem; font-weight: 600;">PDF</span>
                                @else
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #f3f4f6; color: #374151; font-size: 0.8rem; font-weight: 600;">{{ $file->file_type }}</span>
                                @endif
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $file->file_category ?? 'other')) }}</td>
                            <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                            <td>{{ $file->uploaded_at ? $file->uploaded_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                @if($file->file_type === 'text/url')
                                    <a href="{{ $file->file_path }}" target="_blank" style="padding: 0.4rem 0.9rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <i class="fas fa-external-link-alt"></i> Open
                                    </a>
                                @else
                                    <a href="{{ Storage::disk('public')->url($file->file_path) }}" download="{{ $file->file_name }}" style="padding: 0.4rem 0.9rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @if($bonus->evidence_link)
                        <tr>
                            <td style="font-weight: 500;">Evidence Link</td>
                            <td><span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #dbeafe; color: #1e40af; font-size: 0.8rem; font-weight: 600;">URL</span></td>
                            <td>Evidence Link</td>
                            <td>{{ $bonus->user->name ?? 'N/A' }}</td>
                            <td>{{ $bonus->submitted_at ? $bonus->submitted_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ $bonus->evidence_link }}" target="_blank" style="padding: 0.4rem 0.9rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <i class="fas fa-external-link-alt"></i> Open
                                </a>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                @else
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 2rem; text-align: center;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                    <p style="color: #6b7280; font-size: 1rem; margin: 0;">No evidence files or attachments have been uploaded yet.</p>
                </div>
                @endif
            </div>

            <!-- Submission & Affiliation Info -->
            @if($bonus->user)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info" style="color: #3b82f6; margin-right: 0.5rem;"></i>Additional Information
                </h2>
                <div class="detail-grid">
                    @if($bonus->user)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Submitted By</div>
                        <div class="detail-value">{{ $bonus->user->name }}</div>
                        @if($bonus->submitted_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $bonus->submitted_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
