@extends('layouts.public')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $partnership->partner_organization . ' | Partnership/MOU Details - RMS')

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
                    if (str_contains($backUrl, '/partnerships')) {
                        $backText = 'Back to Partnerships';
                    } else {
                        $backUrl = route('partnerships.index');
                        $backText = 'Back to Partnerships';
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
                @if($partnership->status === 'draft' && ($partnership->submitted_by === auth()->id()))
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div style="flex: 1;">
                            <p style="color: #92400e; font-size: 0.95rem; margin: 0; font-weight: 500;">
                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - This partnership/MOU needs to be submitted for approval.
                            </p>
                        </div>
                        <form action="{{ route('partnerships.submit', $partnership->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" style="padding: 0.625rem 1.5rem; background: #f59e0b; border: none; border-radius: 8px; color: white; font-weight: 600; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <!-- Partnership Header -->
            <div class="publication-header">
                <h1 class="publication-title">{{ $partnership->partner_organization }}</h1>
                
                @if($partnership->submitter)
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #374151; font-size: 0.9rem;">Submitted by:</strong>
                    <span style="color: #6b7280; font-size: 0.95rem; margin-left: 0.5rem;">{{ $partnership->submitter->name }}</span>
                </div>
                @endif

                <div class="publication-meta">
                    @if($partnership->type)
                        <div><strong>Type:</strong> {{ strtoupper($partnership->type) }}</div>
                    @endif
                    @if($partnership->date_signed)
                        <div><strong>Date Signed:</strong> {{ $partnership->date_signed->format('F d, Y') }}</div>
                    @endif
                    @if($partnership->year)
                        <div><strong>Year:</strong> {{ $partnership->year }}</div>
                    @endif
                    @if($partnership->leadStaff)
                        <div><strong>Lead Staff:</strong> {{ $partnership->leadStaff->name }}</div>
                    @endif
                </div>

                <div class="publication-badges">
                    @if($partnership->type)
                        <span class="badge-pill" style="background: #eff6ff; color: #1d4ed8;">
                            {{ strtoupper($partnership->type) }}
                        </span>
                    @endif
                    @if($partnership->status)
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
                            $color = $statusColors[$partnership->status] ?? ['bg' => '#6b7280', 'text' => '#fff'];
                        @endphp
                        <span class="badge-pill" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                            {{ strtoupper(str_replace('_', ' ', $partnership->status)) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Scope/Theme Section -->
            @if($partnership->scope_theme)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-file-alt" style="color: #3b82f6; margin-right: 0.5rem;"></i>Scope/Theme
                </h2>
                <p style="color: #374151; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                    {{ $partnership->scope_theme }}
                </p>
            </div>
            @endif

            <!-- Partnership Details Grid -->
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i>Partnership Details
                </h2>
                <div class="detail-grid">
                    @if($partnership->type)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-tag" style="margin-right: 0.5rem;"></i>Type</div>
                        <div class="detail-value">{{ strtoupper($partnership->type) }}</div>
                    </div>
                    @endif

                    @if($partnership->date_signed)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-check" style="margin-right: 0.5rem;"></i>Date Signed</div>
                        <div class="detail-value">{{ $partnership->date_signed->format('F d, Y') }}</div>
                    </div>
                    @endif

                    @if($partnership->expiry_date)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-times" style="margin-right: 0.5rem;"></i>Expiry Date</div>
                        <div class="detail-value">{{ $partnership->expiry_date->format('F d, Y') }}</div>
                    </div>
                    @endif

                    @if($partnership->year)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-alt" style="margin-right: 0.5rem;"></i>Year</div>
                        <div class="detail-value">{{ $partnership->year }}</div>
                    </div>
                    @endif

                    @if($partnership->leadStaff)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-tie" style="margin-right: 0.5rem;"></i>Lead Staff</div>
                        <div class="detail-value">{{ $partnership->leadStaff->name }}</div>
                    </div>
                    @endif

                    @if($partnership->sdg_s)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-globe" style="margin-right: 0.5rem;"></i>SDG Numbers</div>
                        <div class="detail-value">{{ $partnership->sdg_s }}</div>
                    </div>
                    @endif

                    @if($partnership->points_allocated)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-star" style="margin-right: 0.5rem;"></i>Points Allocated</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($partnership->points_allocated, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Outputs Section -->
            @if($partnership->outputs_papers_grants_events)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-chart-line" style="color: #3b82f6; margin-right: 0.5rem;"></i>Outputs (Papers, Grants, Events)
                </h2>
                <p style="color: #374151; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                    {{ $partnership->outputs_papers_grants_events }}
                </p>
            </div>
            @endif

            <!-- Evidence Section -->
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-paperclip" style="color: #3b82f6; margin-right: 0.5rem;"></i>Evidence
                </h2>
                
                @if($partnership->evidence_description)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                    <strong style="color: #374151; display: block; margin-bottom: 0.5rem;">Description:</strong>
                    <p style="color: #4b5563; margin: 0;">{{ $partnership->evidence_description }}</p>
                </div>
                @endif

                @if($partnership->evidence_link)
                <div style="margin-bottom: 1.5rem;">
                    <strong style="color: #374151; display: block; margin-bottom: 0.5rem;">Evidence Link:</strong>
                    <a href="{{ $partnership->evidence_link }}" target="_blank" style="color: #3b82f6; text-decoration: none; word-break: break-all;">
                        <i class="fas fa-external-link-alt"></i> {{ $partnership->evidence_link }}
                    </a>
                </div>
                @endif

                @if($partnership->evidenceFiles && $partnership->evidenceFiles->count() > 0)
                <table class="evidence-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partnership->evidenceFiles as $file)
                        <tr>
                            <td>{{ $file->file_name }}</td>
                            <td>
                                @if($file->file_type === 'text/url')
                                    <span class="badge badge-info">URL</span>
                                @else
                                    <span class="badge badge-secondary">{{ strtoupper(pathinfo($file->file_name, PATHINFO_EXTENSION)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($file->file_type !== 'text/url')
                                    {{ number_format($file->file_size / 1024, 2) }} KB
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $file->uploaded_at ? $file->uploaded_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                @if($file->file_type === 'text/url')
                                    <a href="{{ $file->file_path }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-external-link-alt"></i> Open
                                    </a>
                                @else
                                    <a href="{{ Storage::disk('public')->url($file->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
