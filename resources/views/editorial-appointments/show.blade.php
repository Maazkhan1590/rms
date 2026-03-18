@extends('layouts.public')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $appointment->journal_conference . ' | Editorial Appointment Details - RMS')

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
</style>
@endpush

@section('content')
<section class="publication-detail-page">
    <div class="container">
        <div class="publication-detail-card">
            <a href="{{ route('editorial-appointments.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; margin-bottom: 1.5rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to Editorial Appointments
            </a>

            @auth
                @if($appointment->status === 'draft' && ($appointment->submitted_by === auth()->id()))
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div>
                            <p style="color: #92400e; margin: 0; font-weight: 500;">
                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - Submit for approval.
                            </p>
                        </div>
                        <form action="{{ route('editorial-appointments.submit', $appointment->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" style="padding: 0.625rem 1.5rem; background: #f59e0b; border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <div class="publication-header">
                <h1 class="publication-title">{{ $appointment->journal_conference }}</h1>
                @if($appointment->submitter)
                <div style="margin-bottom: 1rem;">
                    <strong>Submitted by:</strong> {{ $appointment->submitter->name }}
                </div>
                @endif
            
                @php
                    $displayStatus = $appointment->status;
                    $statusColors = [
                        'approved' => ['bg' => '#22c55e', 'text' => '#fff'],
                        'pending' => ['bg' => '#eab308', 'text' => '#fff'],
                        'pending_coordinator' => ['bg' => '#6b7280', 'text' => '#fff'],
                        'pending_dean' => ['bg' => '#6b7280', 'text' => '#fff'],
                        'submitted' => ['bg' => '#3b82f6', 'text' => '#fff'],
                        'rejected' => ['bg' => '#ef4444', 'text' => '#fff'],
                        'draft' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                    ];
                    $color = $statusColors[$displayStatus ?? 'draft'] ?? ['bg' => '#6b7280', 'text' => '#fff'];
                @endphp
                <div class="publication-badges">
                    <span class="badge-pill" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                        {{ strtoupper(str_replace('_', ' ', $displayStatus ?? 'draft')) }}
                    </span>
                </div>
            </div>

            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i>Appointment Details
                </h2>
                <div class="detail-grid">
                    @if($appointment->role)
                    <div class="detail-item">
                        <div class="detail-label">Role</div>
                        <div class="detail-value">{{ $appointment->role }}</div>
                    </div>
                    @endif
                    @if($appointment->start_date)
                    <div class="detail-item">
                        <div class="detail-label">Start Date</div>
                        <div class="detail-value">{{ $appointment->start_date->format('F d, Y') }}</div>
                    </div>
                    @endif
                    @if($appointment->end_date)
                    <div class="detail-item">
                        <div class="detail-label">End Date</div>
                        <div class="detail-value">{{ $appointment->end_date->format('F d, Y') }}</div>
                    </div>
                    @endif
                    @if($appointment->year)
                    <div class="detail-item">
                        <div class="detail-label">Year</div>
                        <div class="detail-value">{{ $appointment->year }}</div>
                    </div>
                    @endif
                    @if($appointment->points_allocated)
                    <div class="detail-item">
                        <div class="detail-label">Points Allocated</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($appointment->points_allocated, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($appointment->notes)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-file-alt" style="color: #3b82f6; margin-right: 0.5rem;"></i>Notes
                </h2>
                <p style="color: #374151; line-height: 1.8; font-size: 1.05rem;">
                    {{ $appointment->notes }}
                </p>
            </div>
            @endif

            @if($appointment->evidence_link || $appointment->evidenceFiles->count() > 0 || $appointment->evidence_description)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-paperclip" style="color: #3b82f6; margin-right: 0.5rem;"></i>Evidence & Attachments
                </h2>
                
                @if($appointment->evidence_description)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                    <strong style="color: #374151; display: block; margin-bottom: 0.5rem;">Description:</strong>
                    <p style="color: #4b5563; margin: 0;">{{ $appointment->evidence_description }}</p>
                </div>
                @endif

                @if($appointment->evidence_link)
                <div style="margin-bottom: 1.5rem;">
                    <strong style="color: #374151; display: block; margin-bottom: 0.5rem;">Evidence Link:</strong>
                    <a href="{{ $appointment->evidence_link }}" target="_blank" style="color: #3b82f6; text-decoration: none; word-break: break-all;">
                        <i class="fas fa-external-link-alt"></i> {{ $appointment->evidence_link }}
                    </a>
                </div>
                @endif

                @if($appointment->evidenceFiles && $appointment->evidenceFiles->count() > 0)
                <table class="evidence-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Uploaded By</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointment->evidenceFiles as $file)
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
                    </tbody>
                </table>
                @else
                <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 2rem; text-align: center;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                    <p style="color: #6b7280; font-size: 1rem; margin: 0;">No evidence files or attachments have been uploaded yet.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Additional Information Section -->
            @if($appointment->submitter || $appointment->approver || isset($appointment->faculty))
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info" style="color: #3b82f6; margin-right: 0.5rem;"></i>Additional Information
                </h2>
                <div class="detail-grid">
                    @if($appointment->submitter)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Submitted By</div>
                        <div class="detail-value">{{ $appointment->submitter->name }}</div>
                        @if($appointment->submitted_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $appointment->submitted_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($appointment->approver)
                    <div class="detail-item">
                        @if($appointment->status === 'rejected')
                            <div class="detail-label"><i class="fas fa-user-times" style="margin-right: 0.5rem;"></i>Rejected By</div>
                        @else
                            <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Approved By</div>
                        @endif
                        <div class="detail-value">{{ $appointment->approver->name }}</div>
                        @if($appointment->approved_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $appointment->approved_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if(isset($appointment->faculty) && $appointment->faculty)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-university" style="margin-right: 0.5rem;"></i>Faculty</div>
                        <div class="detail-value">{{ $appointment->faculty }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

