@extends('layouts.public')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $rtn->title . ' | RTN Submission Details - RMS')

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
                    } elseif (str_contains($backUrl, '/rtn-submissions')) {
                        $backText = 'Back to RTN Submissions';
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
                @if($rtn->status === 'draft' && ($rtn->user_id === auth()->id()))
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div style="flex: 1;">
                            <p style="color: #92400e; font-size: 0.95rem; margin: 0; font-weight: 500;">
                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This RTN submission needs to be submitted for approval.
                            </p>
                        </div>
                        <form action="{{ route('rtn-submissions.submit', $rtn->id) }}" method="POST" style="margin: 0;" class="submit-rtn-form">
                            @csrf
                            <button type="submit" style="padding: 0.625rem 1.5rem; background: #f59e0b; border: none; border-radius: 8px; color: white; font-weight: 600; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <!-- RTN Header -->
            <div class="publication-header">
                <h1 class="publication-title">{{ $rtn->title ?? 'Untitled RTN Submission' }}</h1>
                
                @if($rtn->user)
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #374151; font-size: 0.9rem;">Submitted by:</strong>
                    <span style="color: #6b7280; font-size: 0.95rem; margin-left: 0.5rem;">{{ $rtn->user->name }}</span>
                </div>
                @endif

                <div class="publication-meta">
                    @if($rtn->rtn_type)
                        <div><strong>RTN Type:</strong> {{ $rtn->rtn_type }}</div>
                    @endif
                    @if($rtn->year)
                        <div><strong>Year:</strong> {{ $rtn->year }}</div>
                    @endif
                    @if($rtn->submission_year)
                        <div><strong>Submission Year:</strong> {{ $rtn->submission_year }}</div>
                    @endif
                    @if($rtn->points)
                        <div><strong>Points:</strong> {{ number_format($rtn->points, 2) }}</div>
                    @endif
                </div>

                <div class="publication-badges">
                    @if($rtn->rtn_type)
                        <span class="badge-pill" style="background: #eff6ff; color: #1d4ed8;">
                            {{ strtoupper($rtn->rtn_type) }}
                        </span>
                    @endif
                    @if($rtn->status)
                        @php
                            $statusColors = [
                                'approved' => ['bg' => '#22c55e', 'text' => '#fff'],
                                'pending' => ['bg' => '#eab308', 'text' => '#fff'],
                                'pending_coordinator' => ['bg' => '#6b7280', 'text' => '#fff'],
                                'pending_dean' => ['bg' => '#6b7280', 'text' => '#fff'],
                                'submitted' => ['bg' => '#3b82f6', 'text' => '#fff'],
                                'rejected' => ['bg' => '#ef4444', 'text' => '#fff'],
                                'draft' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                            ];
                            $color = $statusColors[$rtn->status] ?? ['bg' => '#6b7280', 'text' => '#fff'];
                        @endphp
                        <span class="badge-pill" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                            {{ ucfirst($rtn->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Description Section -->
            @if($rtn->description)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-file-alt" style="color: #3b82f6; margin-right: 0.5rem;"></i>Description
                </h2>
                <p style="color: #374151; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                    {{ $rtn->description }}
                </p>
            </div>
            @endif

            <!-- RTN Details Grid -->
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i>RTN Details
                </h2>
                <div class="detail-grid">
                    @if($rtn->rtn_type)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-tag" style="margin-right: 0.5rem;"></i>RTN Type</div>
                        <div class="detail-value">{{ $rtn->rtn_type }}</div>
                    </div>
                    @endif

                    @if($rtn->year)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar" style="margin-right: 0.5rem;"></i>Year</div>
                        <div class="detail-value">{{ $rtn->year }}</div>
                    </div>
                    @endif

                    @if($rtn->submission_year)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-alt" style="margin-right: 0.5rem;"></i>Submission Year</div>
                        <div class="detail-value">{{ $rtn->submission_year }}</div>
                    </div>
                    @endif

                    @if($rtn->units)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calculator" style="margin-right: 0.5rem;"></i>Units</div>
                        <div class="detail-value">{{ $rtn->units }}</div>
                    </div>
                    @endif

                    @if($rtn->amount_omr)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-money-bill-wave" style="margin-right: 0.5rem;"></i>Amount (OMR)</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($rtn->amount_omr, 2) }} OMR</div>
                    </div>
                    @endif

                    @if($rtn->total_rtn)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-chart-bar" style="margin-right: 0.5rem;"></i>Total RTN</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($rtn->total_rtn, 2) }}</div>
                    </div>
                    @endif

                    @if($rtn->points)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-star" style="margin-right: 0.5rem;"></i>Points</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($rtn->points, 2) }}</div>
                    </div>
                    @endif

                    @if($rtn->evidence_description)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <div class="detail-label"><i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>Evidence Description</div>
                        <div class="detail-value">{{ $rtn->evidence_description }}</div>
                    </div>
                    @endif

                    @if($rtn->student_coauthors && is_array($rtn->student_coauthors) && count($rtn->student_coauthors) > 0)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <div class="detail-label"><i class="fas fa-user-graduate" style="margin-right: 0.5rem;"></i>Student Co-authors</div>
                        <div class="detail-value">
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                                @foreach($rtn->student_coauthors as $coauthor)
                                    <span style="padding: 0.35rem 0.85rem; border-radius: 6px; background: #dbeafe; color: #1e40af; font-size: 0.85rem; font-weight: 600;">{{ $coauthor }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($rtn->course_files_updated && is_array($rtn->course_files_updated) && count($rtn->course_files_updated) > 0)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <div class="detail-label"><i class="fas fa-folder-open" style="margin-right: 0.5rem;"></i>Course Files Updated</div>
                        <div class="detail-value">
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                                @foreach($rtn->course_files_updated as $file)
                                    <span style="padding: 0.35rem 0.85rem; border-radius: 6px; background: #d1fae5; color: #065f46; font-size: 0.85rem; font-weight: 600;">{{ $file }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Evidence Files Section -->
            @php
                // Load evidence files directly to avoid any lazy-loading issues
                $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'rtn')
                    ->where('submission_id', $rtn->id)
                    ->with('uploader')
                    ->get();
                $hasLinkEvidence = !empty($rtn->evidence_link);
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

                        @if($rtn->evidence_link)
                        <tr>
                            <td style="font-weight: 500;">Evidence Link</td>
                            <td><span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #dbeafe; color: #1e40af; font-size: 0.8rem; font-weight: 600;">URL</span></td>
                            <td>Evidence Link</td>
                            <td>{{ $rtn->user->name ?? 'N/A' }}</td>
                            <td>{{ $rtn->submitted_at ? $rtn->submitted_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ $rtn->evidence_link }}" target="_blank" style="padding: 0.4rem 0.9rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;">
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
            @php
                $workflow = $rtn->workflow ?? null;
                $approverInfo = null;
                if ($workflow && $workflow->history) {
                    $rejectedHistory = $workflow->history->where('action', 'rejected')->first();
                    $approvedHistory = $workflow->history->where('action', 'approved')->first();
                    if ($rtn->status === 'rejected' && $rejectedHistory) {
                        $approverInfo = ['user' => $rejectedHistory->performer, 'date' => $rejectedHistory->created_at, 'action' => 'rejected'];
                    } elseif ($rtn->status === 'approved' && $approvedHistory) {
                        $approverInfo = ['user' => $approvedHistory->performer, 'date' => $approvedHistory->created_at, 'action' => 'approved'];
                    }
                }
            @endphp
            @if($rtn->user || $approverInfo || $rtn->faculty)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info" style="color: #3b82f6; margin-right: 0.5rem;"></i>Additional Information
                </h2>
                <div class="detail-grid">
                    @if($rtn->user)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Submitted By</div>
                        <div class="detail-value">{{ $rtn->user->name }}</div>
                        @if($rtn->submitted_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $rtn->submitted_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($approverInfo && $approverInfo['user'])
                    <div class="detail-item">
                        @if($approverInfo['action'] === 'rejected')
                            <div class="detail-label"><i class="fas fa-user-times" style="margin-right: 0.5rem;"></i>Rejected By</div>
                        @else
                            <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Approved By</div>
                        @endif
                        <div class="detail-value">{{ $approverInfo['user']->name ?? 'N/A' }}</div>
                        @if($approverInfo['date'])
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $approverInfo['date']->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($rtn->faculty)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-university" style="margin-right: 0.5rem;"></i>Faculty</div>
                        <div class="detail-value">{{ $rtn->faculty }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
