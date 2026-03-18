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

            @if($commercialization->evidence_link || $commercialization->evidenceFiles->count() > 0 || $commercialization->evidence_description)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-paperclip" style="color: #3b82f6; margin-right: 0.5rem;"></i>Evidence
                </h2>
                
                @if($commercialization->evidence_description)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                    <strong style="color: #374151; display: block; margin-bottom: 0.5rem;">Description:</strong>
                    <p style="color: #4b5563; margin: 0;">{{ $commercialization->evidence_description }}</p>
                </div>
                @endif

                @if($commercialization->evidence_link)
                <div style="margin-bottom: 1.5rem;">
                    <strong style="color: #374151; display: block; margin-bottom: 0.5rem;">Evidence Link:</strong>
                    <a href="{{ $commercialization->evidence_link }}" target="_blank" style="color: #3b82f6; text-decoration: none;">
                        <i class="fas fa-external-link-alt"></i> {{ $commercialization->evidence_link }}
                    </a>
                </div>
                @endif

                @if($commercialization->evidenceFiles && $commercialization->evidenceFiles->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($commercialization->evidenceFiles as $file)
                    <div style="padding: 1rem; background: #f9fafb; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <strong>{{ $file->file_name }}</strong>
                            @if($file->file_type !== 'text/url')
                                <span style="color: #6b7280; font-size: 0.875rem;">({{ number_format($file->file_size / 1024, 2) }} KB)</span>
                            @endif
                        </div>
                        @if($file->file_type === 'text/url')
                            <a href="{{ $file->file_path }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-external-link-alt"></i> Open
                            </a>
                        @else
                            <a href="{{ Storage::disk('public')->url($file->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
