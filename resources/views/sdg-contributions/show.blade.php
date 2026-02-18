@extends('layouts.public')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', Str::limit($contribution->title, 50) . ' | SDG Contribution Details - RMS')

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
            <a href="{{ route('sdg-contributions.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; margin-bottom: 1.5rem; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to SDG Contributions
            </a>

            @auth
                @if($contribution->status === 'draft' && ($contribution->submitted_by === auth()->id()))
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div>
                            <p style="color: #92400e; margin: 0; font-weight: 500;">
                                <i class="fas fa-exclamation-circle"></i> <strong>Draft Status</strong> - Submit for approval.
                            </p>
                        </div>
                        <form action="{{ route('sdg-contributions.submit', $contribution->id) }}" method="POST" style="margin: 0;">
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
                <h1 class="publication-title">{{ $contribution->title }}</h1>
                @if($contribution->submitter)
                <div style="margin-bottom: 1rem;">
                    <strong>Submitted by:</strong> {{ $contribution->submitter->name }}
                </div>
                @endif
            </div>

            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i>Contribution Details
                </h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">SDG</div>
                        <div class="detail-value" style="font-weight: 700; font-size: 1.2em;">SDG {{ $contribution->sdg }}</div>
                    </div>
                    @if($contribution->type)
                    <div class="detail-item">
                        <div class="detail-label">Type</div>
                        <div class="detail-value">{{ ucfirst($contribution->type) }}</div>
                    </div>
                    @endif
                    @if($contribution->date)
                    <div class="detail-item">
                        <div class="detail-label">Date</div>
                        <div class="detail-value">{{ $contribution->date->format('F d, Y') }}</div>
                    </div>
                    @endif
                    @if($contribution->year)
                    <div class="detail-item">
                        <div class="detail-label">Year</div>
                        <div class="detail-value">{{ $contribution->year }}</div>
                    </div>
                    @endif
                    @if($contribution->points_allocated)
                    <div class="detail-item">
                        <div class="detail-label">Points Allocated</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($contribution->points_allocated, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($contribution->evidence_link || $contribution->evidenceFiles->count() > 0 || $contribution->evidence_description)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-paperclip" style="color: #3b82f6; margin-right: 0.5rem;"></i>Evidence
                </h2>
                
                @if($contribution->evidence_description)
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                    <strong>Description:</strong>
                    <p style="margin: 0.5rem 0 0 0;">{{ $contribution->evidence_description }}</p>
                </div>
                @endif

                @if($contribution->evidence_link)
                <div style="margin-bottom: 1.5rem;">
                    <strong>Evidence Link:</strong>
                    <a href="{{ $contribution->evidence_link }}" target="_blank" style="color: #3b82f6;">
                        <i class="fas fa-external-link-alt"></i> {{ $contribution->evidence_link }}
                    </a>
                </div>
                @endif

                @if($contribution->evidenceFiles && $contribution->evidenceFiles->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($contribution->evidenceFiles as $file)
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
