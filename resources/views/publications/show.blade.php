@extends('layouts.public')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $publication->title . ' | Academic Research Portal')

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

    .author-card {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        margin: 0.5rem 0.5rem 0.5rem 0;
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
                    } elseif (str_contains($backUrl, '/publications')) {
                        $backText = 'Back to Publications';
                    } elseif (str_contains($backUrl, '/faculty/publications')) {
                        $backText = 'Back to My Publications';
                    }
                }
            @endphp
            <a href="{{ $backUrl ?? route('publications.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; margin-bottom: 1.5rem; font-weight: 500; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> {{ $backText }}
            </a>

            <!-- Submit Button for Draft Publications -->
            @auth
                @if($publication->status === 'draft' && ($publication->submitted_by === auth()->id() || $publication->primary_author_id === auth()->id()))
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div>
                            <h4 style="color: #92400e; font-size: 1rem; font-weight: 600; margin: 0 0 0.25rem 0;">
                                <i class="fas fa-exclamation-circle"></i> Draft Status
                            </h4>
                            <p style="color: #78350f; font-size: 0.9rem; margin: 0;">
                                This publication is in draft status. Submit it for approval to start the review process.
                            </p>
                        </div>
                        <form action="{{ route('publications.submit', $publication->id) }}" method="POST" style="margin: 0;" class="submit-publication-form">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; background: #f59e0b; border: none; border-radius: 8px; color: white; font-weight: 600; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; transition: all 0.3s;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            @endauth

            <!-- Publication Header -->
            <div class="publication-header">
                <h1 class="publication-title">{{ $publication->title ?? 'Untitled Publication' }}</h1>
                
                @php
                    $authorNames = [];
                    if ($publication->authors && is_array($publication->authors)) {
                        foreach ($publication->authors as $author) {
                            $authorNames[] = $author['name'] ?? (is_string($author) ? $author : '');
                        }
                    }
                    if (empty($authorNames) && $publication->submitter) {
                        $authorNames[] = $publication->submitter->name;
                    }
                    if (empty($authorNames) && $publication->primaryAuthor) {
                        $authorNames[] = $publication->primaryAuthor->name;
                    }
                @endphp

                @if(!empty($authorNames))
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #374151; font-size: 0.9rem;">Authors:</strong>
                    <span style="color: #6b7280; font-size: 0.95rem; margin-left: 0.5rem;">{{ implode(', ', array_filter($authorNames)) }}</span>
                </div>
                @endif

                <div class="publication-meta">
                    @if($publication->publication_year || $publication->year)
                        <div><strong>Year:</strong> {{ $publication->publication_year ?? $publication->year ?? 'N/A' }}</div>
                    @endif
                    @if($publication->journal_name || $publication->journal)
                        <div><strong>Journal:</strong> {{ $publication->journal_name ?? $publication->journal ?? 'N/A' }}</div>
                    @endif
                    @if($publication->conference_name)
                        <div><strong>Conference:</strong> {{ $publication->conference_name }}</div>
                    @endif
                    @if($publication->publisher)
                        <div><strong>Publisher:</strong> {{ $publication->publisher }}</div>
                    @endif
                    @if($publication->doi)
                        <div><strong>DOI:</strong> <a href="https://doi.org/{{ $publication->doi }}" target="_blank" style="color: #3b82f6;">{{ $publication->doi }}</a></div>
                    @endif
                </div>

                <div class="publication-badges">
                    <span class="badge-pill" style="background: #eff6ff; color: #1d4ed8;">
                        {{ strtoupper(str_replace('_', ' ', $publication->publication_type ?? 'Publication')) }}
                    </span>
                    @if($publication->status)
                        <span class="badge-pill" style="background: {{ $publication->status === 'approved' ? '#22c55e' : ($publication->status === 'submitted' || $publication->status === 'pending' ? '#eab308' : '#6b7280') }}; color: #fff;">
                            {{ ucfirst($publication->status) }}
                        </span>
                    @endif
                    @if($publication->journal_category)
                        <span class="badge-pill" style="background: #fef3c7; color: #92400e;">
                            {{ ucfirst(str_replace('_', ' ', $publication->journal_category)) }}
                        </span>
                    @endif
                    @if($publication->quartile)
                        <span class="badge-pill" style="background: #dbeafe; color: #1e40af;">
                            {{ $publication->quartile }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Abstract Section -->
            @if($publication->abstract)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-file-alt" style="color: #3b82f6; margin-right: 0.5rem;"></i>Abstract
                </h2>
                <p style="color: #374151; line-height: 1.8; font-size: 1.05rem; text-align: justify;">
                    {{ $publication->abstract }}
                </p>
            </div>
            @endif

            <!-- Publication Details Grid -->
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i>Publication Details
                </h2>
                <div class="detail-grid">
                    @if($publication->journal_name || $publication->journal)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-book" style="margin-right: 0.5rem;"></i>Journal</div>
                        <div class="detail-value">{{ $publication->journal_name ?? $publication->journal ?? 'N/A' }}</div>
                    </div>
                    @endif

                    @if($publication->conference_name)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-users" style="margin-right: 0.5rem;"></i>Conference</div>
                        <div class="detail-value">{{ $publication->conference_name }}</div>
                    </div>
                    @endif

                    @if($publication->publisher)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-building" style="margin-right: 0.5rem;"></i>Publisher</div>
                        <div class="detail-value">{{ $publication->publisher }}</div>
                    </div>
                    @endif

                    @if($publication->doi)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-hashtag" style="margin-right: 0.5rem;"></i>DOI</div>
                        <div class="detail-value">
                            <a href="https://doi.org/{{ $publication->doi }}" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                {{ $publication->doi }} <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i>
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($publication->isbn)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-barcode" style="margin-right: 0.5rem;"></i>ISBN</div>
                        <div class="detail-value">{{ $publication->isbn }}</div>
                    </div>
                    @endif

                    @if($publication->indexing_db)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-database" style="margin-right: 0.5rem;"></i>Indexing Database</div>
                        <div class="detail-value">{{ $publication->indexing_db }}</div>
                    </div>
                    @endif

                    @if($publication->publication_year || $publication->year)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar" style="margin-right: 0.5rem;"></i>Publication Year</div>
                        <div class="detail-value">{{ $publication->publication_year ?? $publication->year ?? 'N/A' }}</div>
                    </div>
                    @endif

                    @if($publication->published_at)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-check" style="margin-right: 0.5rem;"></i>Published Date</div>
                        <div class="detail-value">{{ $publication->published_at->format('F d, Y') }}</div>
                    </div>
                    @endif

                    @if($publication->submission_year)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-alt" style="margin-right: 0.5rem;"></i>Submission Year</div>
                        <div class="detail-value">{{ $publication->submission_year }}</div>
                    </div>
                    @endif

                    @if($publication->approved_at)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>Approved Date</div>
                        <div class="detail-value">{{ $publication->approved_at->format('F d, Y') }}</div>
                    </div>
                    @endif

                    @if(!is_null($publication->sohar_affiliation))
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-university" style="margin-right: 0.5rem;"></i>Sohar Affiliation</div>
                        <div class="detail-value">
                            <span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: {{ $publication->sohar_affiliation ? '#d1fae5' : '#f3f4f6' }}; color: {{ $publication->sohar_affiliation ? '#065f46' : '#6b7280' }};">
                                {{ $publication->sohar_affiliation ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    @endif

                    @if(!is_null($publication->percent_contribution))
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-percentage" style="margin-right: 0.5rem;"></i>Contribution</div>
                        <div class="detail-value">{{ number_format($publication->percent_contribution, 2) }}%</div>
                    </div>
                    @endif

                    @if($publication->su_author_type)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-tag" style="margin-right: 0.5rem;"></i>SU Author Type</div>
                        <div class="detail-value">{{ $publication->su_author_type }}</div>
                    </div>
                    @endif

                    @if(!is_null($publication->student_coauthor))
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-graduate" style="margin-right: 0.5rem;"></i>Student Co-author</div>
                        <div class="detail-value">
                            <span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: {{ $publication->student_coauthor ? '#d1fae5' : '#f3f4f6' }}; color: {{ $publication->student_coauthor ? '#065f46' : '#6b7280' }};">
                                {{ $publication->student_coauthor ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    @endif

                    @if($publication->student_level)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-graduation-cap" style="margin-right: 0.5rem;"></i>Student Level</div>
                        <div class="detail-value">{{ $publication->student_level }}</div>
                    </div>
                    @endif

                    @if($publication->points_allocated)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-star" style="margin-right: 0.5rem;"></i>Research Points</div>
                        <div class="detail-value" style="font-weight: 700; color: #059669;">{{ number_format($publication->points_allocated, 2) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Authors Section -->
            @php
                $allAuthors = [];
                if ($publication->authors && is_array($publication->authors)) {
                    foreach ($publication->authors as $author) {
                        $name = $author['name'] ?? (is_string($author) ? $author : '');
                        if ($name) $allAuthors[] = $name;
                    }
                }
                if ($publication->co_authors && is_array($publication->co_authors)) {
                    foreach ($publication->co_authors as $coAuthor) {
                        $name = $coAuthor['name'] ?? (is_string($coAuthor) ? $coAuthor : '');
                        if ($name && !in_array($name, $allAuthors)) $allAuthors[] = $name;
                    }
                }
                if (empty($allAuthors) && $publication->submitter) {
                    $allAuthors[] = $publication->submitter->name;
                }
                if (empty($allAuthors) && $publication->primaryAuthor) {
                    $allAuthors[] = $publication->primaryAuthor->name;
                }
            @endphp
            @if(!empty($allAuthors))
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-users" style="color: #3b82f6; margin-right: 0.5rem;"></i>Authors
                </h2>
                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                    @foreach($allAuthors as $authorName)
                    <div class="author-card">
                        <i class="fas fa-user-circle" style="color: #3b82f6; font-size: 1.25rem;"></i>
                        <span style="color: #111827; font-weight: 500;">{{ $authorName }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Evidence Files Section -->
            @php
                // Load evidence files directly from database to ensure they're loaded
                $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'publication')
                    ->where('submission_id', $publication->id)
                    ->with('uploader')
                    ->get();
                $hasLinkEvidence = !empty($publication->published_link) || !empty($publication->proceedings_link) || !empty($publication->acceptance_letter_path);
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

                        @if($publication->published_link)
                        <tr>
                            <td style="font-weight: 500;">Published Link</td>
                            <td><span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #dbeafe; color: #1e40af; font-size: 0.8rem; font-weight: 600;">URL</span></td>
                            <td>Published Link</td>
                            <td>{{ $publication->submitter->name ?? 'N/A' }}</td>
                            <td>{{ $publication->submitted_at ? $publication->submitted_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ $publication->published_link }}" target="_blank" style="padding: 0.4rem 0.9rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <i class="fas fa-external-link-alt"></i> Open
                                </a>
                            </td>
                        </tr>
                        @endif

                        @if($publication->proceedings_link)
                        <tr>
                            <td style="font-weight: 500;">Proceedings Link</td>
                            <td><span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #dbeafe; color: #1e40af; font-size: 0.8rem; font-weight: 600;">URL</span></td>
                            <td>Proceedings Link</td>
                            <td>{{ $publication->submitter->name ?? 'N/A' }}</td>
                            <td>{{ $publication->submitted_at ? $publication->submitted_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ $publication->proceedings_link }}" target="_blank" style="padding: 0.4rem 0.9rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <i class="fas fa-external-link-alt"></i> Open
                                </a>
                            </td>
                        </tr>
                        @endif

                        @if($publication->acceptance_letter_path)
                        <tr>
                            <td style="font-weight: 500;">Acceptance Letter</td>
                            <td><span style="padding: 0.25rem 0.75rem; border-radius: 6px; background: #fee2e2; color: #991b1b; font-size: 0.8rem; font-weight: 600;">PDF</span></td>
                            <td>Acceptance Letter</td>
                            <td>{{ $publication->submitter->name ?? 'N/A' }}</td>
                            <td>{{ $publication->submitted_at ? $publication->submitted_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ Storage::disk('public')->url($publication->acceptance_letter_path) }}" download style="padding: 0.4rem 0.9rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <i class="fas fa-download"></i> Download
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
            @if($publication->submitter || $publication->approver || $publication->college || $publication->department)
            <div style="margin-bottom: 2.5rem;">
                <h2 class="section-title">
                    <i class="fas fa-info" style="color: #3b82f6; margin-right: 0.5rem;"></i>Additional Information
                </h2>
                <div class="detail-grid">
                    @if($publication->submitter)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Submitted By</div>
                        <div class="detail-value">{{ $publication->submitter->name }}</div>
                        @if($publication->submitted_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $publication->submitted_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($publication->approver)
                    <div class="detail-item">
                        @if($publication->status === 'rejected')
                            <div class="detail-label"><i class="fas fa-user-times" style="margin-right: 0.5rem;"></i>Rejected By</div>
                        @else
                            <div class="detail-label"><i class="fas fa-user-check" style="margin-right: 0.5rem;"></i>Approved By</div>
                        @endif
                        <div class="detail-value">{{ $publication->approver->name }}</div>
                        @if($publication->approved_at)
                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.5rem;">
                            <i class="far fa-calendar"></i> {{ $publication->approved_at->format('F d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($publication->college)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-university" style="margin-right: 0.5rem;"></i>College</div>
                        <div class="detail-value">{{ $publication->college }}</div>
                    </div>
                    @endif

                    @if($publication->department)
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-building" style="margin-right: 0.5rem;"></i>Department</div>
                        <div class="detail-value">{{ $publication->department }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- External Links -->
            @if($publication->published_link || $publication->proceedings_link)
            <div style="padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                <h2 class="section-title" style="margin-bottom: 1rem;">
                    <i class="fas fa-link" style="color: #3b82f6; margin-right: 0.5rem;"></i>External Links
                </h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    @if($publication->published_link)
                    <a href="{{ $publication->published_link }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.75rem; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                        <i class="fas fa-external-link-alt"></i> View Publication
                    </a>
                    @endif
                    @if($publication->proceedings_link)
                    <a href="{{ $publication->proceedings_link }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.75rem; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 600; border: 2px solid #e5e7eb; transition: all 0.3s;">
                        <i class="fas fa-file-pdf"></i> View Proceedings
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
