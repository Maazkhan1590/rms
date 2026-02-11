@extends('layouts.admin')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('page-title', 'Publication Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $publication->title }}</h3>
                    <div>
                        @if($publication->status === 'draft')
                            <a href="{{ route('faculty.publications.edit', $publication) }}" class="btn btn-primary btn-sm">
                                <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">edit</span>
                                <span style="vertical-align: middle;">Edit</span>
                            </a>
                            <form action="{{ route('faculty.publications.submit', $publication) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Submit this publication for approval?')">
                                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">send</span>
                                    <span style="vertical-align: middle;">Submit for Approval</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $workflow = $publication->workflow;
                    $authors = is_array($publication->authors ?? null) ? $publication->authors : [];
                    $evidenceFiles = $publication->evidenceFiles ?? collect();
                    $status = $publication->status ?? 'draft';
                @endphp

                <!-- Summary strip -->
                <div class="submission-summary">
                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 0.75rem;">
                        <div>
                            <span class="text-muted">Status</span><br>
                            @if($status === 'approved')
                                <span class="badge badge-success">Approved</span>
                            @elseif(in_array($status, ['pending_coordinator', 'pending_dean', 'submitted']))
                                <span class="badge badge-warning">In Review</span>
                            @elseif($status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-secondary">Draft</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-muted">Points Allocated</span><br>
                            <span class="badge badge-info">{{ number_format($publication->points_allocated ?? 0, 1) }}</span>
                        </div>
                        <div>
                            <span class="text-muted">Submitted At</span><br>
                            <span>{{ $publication->submitted_at ? $publication->submitted_at->format('M d, Y') : '—' }}</span>
                        </div>
                        <div>
                            <span class="text-muted">Approval</span><br>
                            @if($workflow)
                                <span class="badge badge-{{ $workflow->status === 'approved' ? 'success' : (in_array($workflow->status, ['pending_coordinator','pending_dean']) ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $workflow->status)) }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Not Started</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Read-only Submission Stepper (like create form) -->
                <div class="submission-stepper-container">
                    <div class="submission-stepper">
                        <div class="submission-stepper-line" id="submissionStepperLine"></div>
                        <button type="button" class="submission-step-item active" data-step="1" onclick="goSubmissionStep(1)">
                            <div class="submission-step-circle active">1</div>
                            <div class="submission-step-label">Basic Info</div>
                        </button>
                        <button type="button" class="submission-step-item" data-step="2" onclick="goSubmissionStep(2)">
                            <div class="submission-step-circle">2</div>
                            <div class="submission-step-label">Details</div>
                        </button>
                        <button type="button" class="submission-step-item" data-step="3" onclick="goSubmissionStep(3)">
                            <div class="submission-step-circle">3</div>
                            <div class="submission-step-label">Authors</div>
                        </button>
                        <button type="button" class="submission-step-item" data-step="4" onclick="goSubmissionStep(4)">
                            <div class="submission-step-circle">4</div>
                            <div class="submission-step-label">Evidence</div>
                        </button>
                    </div>
                </div>

                <!-- Step 1: Basic Info -->
                <div class="submission-step-content" data-step="1">
                    <h5 class="submission-step-title">Basic Information</h5>
                    <table class="table table-bordered table-sm mb-0">
                        <tr>
                            <th width="220">Publication Title</th>
                            <td>{{ $publication->title }}</td>
                        </tr>
                        <tr>
                            <th>Publication Type</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) }}</td>
                        </tr>
                        <tr>
                            <th>Publication Year</th>
                            <td>{{ $publication->year ?? $publication->publication_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Abstract</th>
                            <td>{{ $publication->abstract ?: '—' }}</td>
                        </tr>
                    </table>

                    <div class="submission-step-actions">
                        <div></div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="nextSubmissionStep()">
                            <span>Next</span>
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Details -->
                <div class="submission-step-content" data-step="2" style="display:none;">
                    <h5 class="submission-step-title">Publication Details</h5>
                    <table class="table table-bordered table-sm mb-0">
                        <tr>
                            <th width="220">Journal Category</th>
                            <td>{{ $publication->journal_category ? ucfirst(str_replace('_', ' ', $publication->journal_category)) : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Quartile</th>
                            <td>{{ $publication->quartile ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Journal Name</th>
                            <td>{{ $publication->journal_name ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Conference Name</th>
                            <td>{{ $publication->conference_name ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Publisher</th>
                            <td>{{ $publication->publisher ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>DOI</th>
                            <td>
                                @if($publication->doi)
                                    <a href="https://doi.org/{{ $publication->doi }}" target="_blank">{{ $publication->doi }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>ISBN</th>
                            <td>{{ $publication->isbn ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Indexing DB</th>
                            <td>{{ $publication->indexing_db ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Sohar Affiliation</th>
                            <td>
                                @if(!is_null($publication->sohar_affiliation))
                                    <span class="badge badge-{{ $publication->sohar_affiliation ? 'success' : 'secondary' }}">{{ $publication->sohar_affiliation ? 'Yes' : 'No' }}</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>% Contribution</th>
                            <td>{{ !is_null($publication->percent_contribution) ? number_format($publication->percent_contribution, 2) . '%' : '—' }}</td>
                        </tr>
                        <tr>
                            <th>SU Author Type</th>
                            <td>{{ $publication->su_author_type ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Student Co-author</th>
                            <td>
                                @if(!is_null($publication->student_coauthor))
                                    <span class="badge badge-{{ $publication->student_coauthor ? 'success' : 'secondary' }}">{{ $publication->student_coauthor ? 'Yes' : 'No' }}</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Student Level</th>
                            <td>{{ $publication->student_level ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Published Link</th>
                            <td>
                                @if($publication->published_link)
                                    <a href="{{ $publication->published_link }}" target="_blank">{{ $publication->published_link }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Proceedings Link</th>
                            <td>
                                @if($publication->proceedings_link)
                                    <a href="{{ $publication->proceedings_link }}" target="_blank">{{ $publication->proceedings_link }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="submission-step-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="prevSubmissionStep()">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_back</span>
                            <span>Previous</span>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="nextSubmissionStep()">
                            <span>Next</span>
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Authors -->
                <div class="submission-step-content" data-step="3" style="display:none;">
                    <h5 class="submission-step-title">Authors</h5>
                    @if(count($authors) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Primary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($authors as $author)
                                        <tr>
                                            <td>{{ $author['name'] ?? '—' }}</td>
                                            <td>{{ $author['email'] ?? '—' }}</td>
                                            <td>
                                                @if(!empty($author['is_primary']))
                                                    <span class="badge badge-success">Primary</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">No authors were provided.</div>
                    @endif

                    <div class="submission-step-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="prevSubmissionStep()">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_back</span>
                            <span>Previous</span>
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="nextSubmissionStep()">
                            <span>Next</span>
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Evidence -->
                <div class="submission-step-content" data-step="4" style="display:none;">
                    <h5 class="submission-step-title">Evidence / Attachments</h5>
                    @php
                        $hasLinkEvidence = !empty($publication->published_link) || !empty($publication->proceedings_link);
                        $hasAnyEvidence = $evidenceFiles->count() > 0 || $hasLinkEvidence;
                    @endphp
                    @if($hasAnyEvidence)
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Evidence</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Uploaded By</th>
                                        <th>Upload Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evidenceFiles as $file)
                                        <tr>
                                            <td>{{ $file->file_name }}</td>
                                            <td>
                                                @if($file->file_type === 'text/url')
                                                    <span class="badge badge-info">URL</span>
                                                @elseif(str_contains($file->file_type, 'image'))
                                                    <span class="badge badge-success">Image</span>
                                                @elseif(str_contains($file->file_type, 'pdf'))
                                                    <span class="badge badge-danger">PDF</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $file->file_type }}</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $file->file_category ?? 'other')) }}</td>
                                            <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                                            <td>{{ $file->uploaded_at ? $file->uploaded_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                            <td>
                                                @if($file->file_type === 'text/url')
                                                    <a href="{{ $file->file_path }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">open_in_new</span>
                                                        <span style="vertical-align: middle;">Open</span>
                                                    </a>
                                                @else
                                                    <a href="{{ Storage::disk('public')->url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">download</span>
                                                        <span style="vertical-align: middle;">Download</span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if($publication->published_link)
                                        <tr>
                                            <td>Published Link</td>
                                            <td><span class="badge badge-info">URL</span></td>
                                            <td>published_link</td>
                                            <td>{{ $publication->submitter->name ?? 'N/A' }}</td>
                                            <td>{{ $publication->submitted_at ? $publication->submitted_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ $publication->published_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">open_in_new</span>
                                                    <span style="vertical-align: middle;">Open</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif

                                    @if($publication->proceedings_link)
                                        <tr>
                                            <td>Proceedings Link</td>
                                            <td><span class="badge badge-info">URL</span></td>
                                            <td>proceedings_link</td>
                                            <td>{{ $publication->submitter->name ?? 'N/A' }}</td>
                                            <td>{{ $publication->submitted_at ? $publication->submitted_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ $publication->proceedings_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <span class="material-icons-outlined" style="font-size:16px;vertical-align:middle;">open_in_new</span>
                                                    <span style="vertical-align: middle;">Open</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">No evidence/attachments uploaded.</div>
                    @endif

                    <div class="submission-step-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="prevSubmissionStep()">
                            <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_back</span>
                            <span>Previous</span>
                        </button>
                        <div></div>
                    </div>
                </div>
            </div>
        </div>

        @if($publication->workflow && $publication->workflow->history->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Approval History</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($publication->workflow->history as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <strong>{{ $history->performer->name }}</strong>
                            <span class="badge badge-{{ $history->action === 'approved' ? 'success' : ($history->action === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($history->action) }}
                            </span>
                            <p class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</p>
                            @if($history->comments)
                                <p>{{ $history->comments }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('faculty.publications.index') }}" class="btn btn-secondary btn-block">
                    <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">arrow_back</span>
                    <span style="vertical-align: middle;">Back to List</span>
                </a>
                @if($publication->status === 'draft')
                <a href="{{ route('faculty.publications.edit', $publication) }}" class="btn btn-primary btn-block">
                    <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">edit</span>
                    <span style="vertical-align: middle;">Edit Publication</span>
                </a>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Approval Progress</h3>
            </div>
            <div class="card-body">
                @if($workflow)
                    <p class="mb-1"><strong>Workflow Status:</strong> {{ ucfirst(str_replace('_', ' ', $workflow->status)) }}</p>
                    <p class="mb-1"><strong>Current Step:</strong>
                        @if($workflow->current_step == 1)
                            Faculty Submission
                        @elseif($workflow->current_step == 2)
                            Coordinator Review
                        @elseif($workflow->current_step == 3)
                            Dean Review
                        @else
                            Step {{ $workflow->current_step }}
                        @endif
                    </p>
                    @if($workflow->fallback_used)
                        <p class="mb-1"><span class="badge badge-warning">Fallback Workflow</span> Coordinator step skipped.</p>
                    @endif
                    @if($workflow->assignee)
                        <p class="mb-0"><strong>Assigned To:</strong> {{ $workflow->assignee->name }}</p>
                    @else
                        <p class="mb-0 text-muted">Not assigned yet.</p>
                    @endif
                @else
                    <p class="mb-0 text-muted">Workflow not started yet. It will begin after you submit for approval.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -8px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--color-primary);
        border: 2px solid white;
    }

    .timeline-content {
        padding-left: 20px;
    }

/* Summary strip */
.submission-summary {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.9rem 1rem;
    margin-bottom: 1.25rem;
}

/* Read-only submission stepper (matches create experience) */
.submission-stepper-container {
    margin: 1rem 0 1.25rem;
}

.submission-stepper {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0 0.25rem;
}

.submission-stepper::before {
    content: '';
    position: absolute;
    top: 22px;
    left: 8%;
    right: 8%;
    height: 3px;
    background: #e5e7eb;
    border-radius: 2px;
    z-index: 1;
}

.submission-stepper-line {
    position: absolute;
    top: 22px;
    left: 8%;
    height: 3px;
    background: #2563eb;
    border-radius: 2px;
    z-index: 2;
    width: 0%;
    transition: width 0.25s ease;
}

.submission-step-item {
    background: transparent;
    border: none;
    padding: 0;
    flex: 1;
    min-width: 0;
    cursor: pointer;
    z-index: 3;
    text-align: center;
}

.submission-step-circle {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.4rem;
    font-weight: 700;
    background: #f3f4f6;
    color: #6b7280;
    border: 3px solid #e5e7eb;
    transition: all 0.2s ease;
}

.submission-step-circle.active {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
    transform: scale(1.05);
}

.submission-step-circle.completed {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}

.submission-step-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    line-height: 1.1;
}

.submission-step-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 0.75rem;
    color: #111827;
}

.submission-step-actions {
    display: flex;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}
</style>
@endsection

@section('scripts')
<script>
    let submissionCurrentStep = 1;
    const submissionTotalSteps = 4;

    function updateSubmissionStepper() {
        // Show only the current step content
        document.querySelectorAll('.submission-step-content').forEach(function(el) {
            el.style.display = el.getAttribute('data-step') == submissionCurrentStep ? 'block' : 'none';
        });

        // Update step circles
        document.querySelectorAll('.submission-step-item').forEach(function(item) {
            const stepNum = parseInt(item.getAttribute('data-step'), 10);
            const circle = item.querySelector('.submission-step-circle');
            circle.classList.remove('active', 'completed');

            if (stepNum < submissionCurrentStep) {
                circle.classList.add('completed');
            } else if (stepNum === submissionCurrentStep) {
                circle.classList.add('active');
            }
        });

        // Update progress line
        const line = document.getElementById('submissionStepperLine');
        if (!line) return;
        if (submissionCurrentStep <= 1) {
            line.style.width = '0%';
        } else {
            const progress = ((submissionCurrentStep - 1) / (submissionTotalSteps - 1)) * 84; // match left/right 8%
            line.style.width = progress + '%';
        }
    }

    function goSubmissionStep(step) {
        submissionCurrentStep = Math.max(1, Math.min(submissionTotalSteps, step));
        updateSubmissionStepper();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextSubmissionStep() {
        goSubmissionStep(submissionCurrentStep + 1);
    }

    function prevSubmissionStep() {
        goSubmissionStep(submissionCurrentStep - 1);
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateSubmissionStepper();
    });
</script>
@endsection
