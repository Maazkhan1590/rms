@extends('layouts.public')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $commercialization->product_service_name . ' | Commercialization Details - RMS')

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
            <a href="{{ route('commercializations.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; margin-bottom: 1.5rem; font-weight: 500; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Back to Commercializations
            </a>

            @auth
                @if($commercialization->status === 'draft' && ($commercialization->submitted_by === auth()->id()))
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div style="flex: 1;">
                            <p style="color: #92400e; font-size: 0.95rem; margin: 0; font-weight: 500;">
                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This commercialization needs to be submitted for approval.
                            </p>
                        </div>
                        <form action="{{ route('commercializations.submit', $commercialization->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" style="padding: 0.625rem 1.5rem; background: #f59e0b; border: none; border-radius: 8px; color: white; font-weight: 600; font-size: 0.95rem; cursor: pointer;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <div class="publication-header">
                <h1 class="publication-title">{{ $commercialization->product_service_name }}</h1>
                
                @if($commercialization->submitter)
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #374151; font-size: 0.9rem;">Submitted by:</strong>
                    <span style="color: #6b7280; font-size: 0.95rem; margin-left: 0.5rem;">{{ $commercialization->submitter->name }}</span>
                </div>
                @endif
            
                @php
                    $displayStatus = $commercialization->status;
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
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i>Commercialization Details
                </h2>
                <div class="detail-grid">
                    @if($commercialization->type)
                    <div class="detail-item">
                        <div class="detail-label">Type</div>
                        <div class="detail-value">{{ ucfirst($commercialization->type) }}</div>
                    </div>
                    @endif

                    @if($commercialization->stage)
                    <div class="detail-item">
                        <div class="detail-label">Stage</div>
                        <div class="detail-value">{{ ucfirst($commercialization->stage) }}</div>
                    </div>
                    @endif

                    @if($commercialization->launch_date)
                    <div class="detail-item">
                        <div class="detail-label">Launch Date</div>
                        <div class="detail-value">{{ $commercialization->launch_date->format('F d, Y') }}</div>
                    </div>
                    @endif

                    @if($commercialization->revenue_omr)
                    <div class="detail-item">
                        <div class="detail-label">Revenue (OMR)</div>
                        <div class="detail-value">{{ number_format($commercialization->revenue_omr, 2) }}</div>
                    </div>
                    @endif

                    @if($commercialization->ip_patent)
                    <div class="detail-item">
                        <div class="detail-label">IP/Patent</div>
                        <div class="detail-value"><i class="fas fa-check-circle" style="color: green;"></i> Registered</div>
                    </div>
                    @endif

                    @if($commercialization->year)
                    <div class="detail-item">
                        <div class="detail-label">Year</div>
                        <div class="detail-value">{{ $commercialization->year }}</div>
                    </div>
                    @endif

                    @if($commercialization->points_allocated)
                    <div class="detail-item">
                        <div class="detail-label">Points Allocated</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($commercialization->points_allocated, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($commercialization->client_market)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-users" style="color: #3b82f6; margin-right: 0.5rem;"></i>Client/Market
                </h2>
                <p style="color: #374151; line-height: 1.8; font-size: 1.05rem;">
                    {{ $commercialization->client_market }}
                </p>
            </div>
            @endif

            <!-- Evidence & Attachments Section -->
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-paperclip" style="color: #3b82f6; margin-right: 0.5rem;"></i>Evidence & Attachments
                </h2>
                @if($commercialization->evidenceFiles && $commercialization->evidenceFiles->count() > 0)
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
                        @foreach($commercialization->evidenceFiles as $file)
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

            <!-- Additional Information Section -->
            @if($commercialization->submitter || $commercialization->approver || isset($commercialization->faculty))
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info" style="color: #3b82f6; margin-right: 0.5rem;"></i>Additional Information
                </h2>
                <div class="detail-grid">
                    @if($commercialization->submitter)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Submitted By</div>
                        <div class="detail-value">{{ $commercialization->submitter->name }}</div>
                        @if($commercialization->submitted_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $commercialization->submitted_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($commercialization->approver)
                    <div class="detail-item">
                        @if($commercialization->status === 'rejected')
                            <div class="detail-label"><i class="fas fa-user-times" style="margin-right: 0.5rem;"></i>Rejected By</div>
                        @else
                            <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Approved By</div>
                        @endif
                        <div class="detail-value">{{ $commercialization->approver->name }}</div>
                        @if($commercialization->approved_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $commercialization->approved_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if(isset($commercialization->faculty) && $commercialization->faculty)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-university" style="margin-right: 0.5rem;"></i>Faculty</div>
                        <div class="detail-value">{{ $commercialization->faculty }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Approval & Workflow Timeline Section -->
            @if($commercialization->workflow)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-history" style="color: #3b82f6; margin-right: 0.5rem;"></i>Approval Timeline
                </h2>

                @php
                    $historyEntries = ($commercialization->workflow && $commercialization->workflow->history)
                        ? $commercialization->workflow->history->sortBy('created_at')->values()
                        : collect();

                    $hasDraftEntry = $historyEntries->contains(function ($entry) {
                        return isset($entry->new_status) && $entry->new_status === 'draft';
                    });

                    if (!$hasDraftEntry && $commercialization->workflow && $commercialization->workflow->created_at) {
                        $draftEntry = new \stdClass();
                        $draftEntry->action = 'submitted';
                        $draftEntry->new_status = 'draft';
                        $draftEntry->previous_status = null;
                        $draftEntry->comments = 'Draft created';
                        $draftEntry->created_at = $commercialization->workflow->created_at;
                        $draftEntry->performer = $commercialization->workflow->submitter;

                        $historyEntries = $historyEntries
                            ->prepend($draftEntry)
                            ->sortBy(function ($entry) {
                                if (is_object($entry->created_at) && method_exists($entry->created_at, 'getTimestamp')) {
                                    return $entry->created_at->getTimestamp();
                                }
                                if (is_string($entry->created_at)) {
                                    return strtotime($entry->created_at);
                                }
                                return 0;
                            })
                            ->values();
                    }
                @endphp

                @if($historyEntries->count() > 0)
                    <div class="approval-timeline" style="margin-top: 1.5rem;">
                        @foreach($historyEntries as $history)
                            @php
                                $action = isset($history->action) ? $history->action : 'submitted';
                                $newStatus = isset($history->new_status) ? $history->new_status : null;
                                $previousStatus = isset($history->previous_status) ? $history->previous_status : null;

                                $isApproved = $action === 'approved';
                                $isRejected = $action === 'rejected';
                                $isDraft = $newStatus === 'draft';

                                if ($isApproved) {
                                    $iconColor = '#22c55e';
                                    $iconBg = '#f0fdf4';
                                    $icon = 'check_circle';
                                } elseif ($isRejected) {
                                    $iconColor = '#ef4444';
                                    $iconBg = '#fef2f2';
                                    $icon = 'cancel';
                                } elseif ($isDraft) {
                                    $iconColor = '#f59e0b';
                                    $iconBg = '#fef3c7';
                                    $icon = 'drafts';
                                } else {
                                    $iconColor = '#3b82f6';
                                    $iconBg = '#eff6ff';
                                    $icon = 'pending';
                                }

                                $performerName = 'N/A';
                                if (isset($history->performer) && is_object($history->performer) && isset($history->performer->name)) {
                                    $performerName = $history->performer->name;
                                } elseif (isset($history->performer) && is_string($history->performer)) {
                                    $performerName = $history->performer;
                                }

                                $formattedTime = 'N/A';
                                if (isset($history->created_at) && is_object($history->created_at) && method_exists($history->created_at, 'format')) {
                                    $formattedTime = $history->created_at->format('M d, Y H:i');
                                } elseif (isset($history->created_at) && is_string($history->created_at)) {
                                    $formattedTime = date('M d, Y H:i', strtotime($history->created_at));
                                }
                            @endphp

                            <div style="position: relative; padding-left: 48px; padding-bottom: 20px;">
                                @if(!$loop->last)
                                    <div style="position: absolute; left: 18px; top: 36px; bottom: -6px; width: 2px; background: linear-gradient(180deg, {{ $iconColor }} 0%, #e5e7eb 100%);"></div>
                                @endif

                                <div style="position: absolute; left: 0; top: 0; width: 36px; height: 36px; border-radius: 50%; background: {{ $iconBg }}; border: 2px solid {{ $iconColor }}; display: flex; align-items: center; justify-content: center; z-index: 1;">
                                    @if($isApproved)
                                        <i class="fas fa-check" style="font-size: 18px; color: {{ $iconColor }};"></i>
                                    @elseif($isRejected)
                                        <i class="fas fa-times" style="font-size: 18px; color: {{ $iconColor }};"></i>
                                    @elseif($isDraft)
                                        <i class="fas fa-file-alt" style="font-size: 18px; color: {{ $iconColor }};"></i>
                                    @else
                                        <i class="fas fa-hourglass-half" style="font-size: 18px; color: {{ $iconColor }};"></i>
                                    @endif
                                </div>

                                <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; flex-wrap: wrap;">
                                        <div>
                                            <span style="display: inline-block; background: {{ $iconColor }}; color: #fff; font-size: 12px; padding: 4px 8px; border-radius: 4px; font-weight: 600;">{{ ucfirst($action) }}</span>
                                            @if($previousStatus || $newStatus)
                                                <span style="font-size: 12px; color: #6b7280; margin-left: 6px;">
                                                    {{ $previousStatus ? ucwords(str_replace('_', ' ', $previousStatus)) : 'N/A' }}
                                                    @if($newStatus) → {{ ucwords(str_replace('_', ' ', $newStatus)) }} @endif
                                                </span>
                                            @endif
                                        </div>
                                        <span style="font-size: 12px; color: #6b7280; white-space: nowrap;">{{ $formattedTime }}</span>
                                    </div>

                                    <div style="margin-top: 8px; font-size: 14px; color: #111827;">
                                        <strong>{{ $performerName }}</strong>
                                    </div>

                                    @if(isset($history->comments) && $history->comments)
                                        <div style="margin-top: 8px; padding: 8px; background: #f9fafb; border-left: 3px solid {{ $iconColor }}; border-radius: 4px;">
                                            <div style="font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 600;">Comments</div>
                                            <div style="font-size: 13px; color: #374151;">{{ $history->comments }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 2rem; text-align: center;">
                        <i class="fas fa-info-circle" style="font-size: 2rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                        <p style="color: #6b7280; font-size: 1rem; margin: 0;">No approval history available.</p>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

