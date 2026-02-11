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
                    $workflowStatus = $workflow->status ?? $publication->status;
                    $currentStep = $workflow->current_step ?? 1;

                    // Build dynamic visual steps based on workflow configuration
                    // Default: Faculty → Coordinator → Dean
                    // Fallback: Faculty → Dean (coordinator skipped, current_step jumps to 3)
                    if ($workflow && $workflow->fallback_used) {
                        $steps = [
                            1 => ['label' => 'Faculty Submission', 'db_step' => 1],
                            2 => ['label' => 'Dean Review', 'db_step' => 3],
                        ];
                    } else {
                        $steps = [
                            1 => ['label' => 'Faculty Submission', 'db_step' => 1],
                            2 => ['label' => 'Coordinator Review', 'db_step' => 2],
                            3 => ['label' => 'Dean Review', 'db_step' => 3],
                        ];
                    }
                @endphp

                <!-- Workflow Stepper (dynamic based on workflow / fallback) -->
                <div class="mb-4">
                    <div class="stepper d-flex justify-content-between align-items-center">
                        @foreach($steps as $visualStep => $step)
                            @php
                                $dbStep = $step['db_step'];
                                $isCompleted = $workflowStatus === 'approved' || $currentStep > $dbStep;
                                $isActive = $currentStep === $dbStep && $workflowStatus !== 'approved';
                            @endphp
                            <div class="step-item text-center flex-fill">
                                <div class="step-circle {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                                    {{ $visualStep }}
                                </div>
                                <div class="step-label">{{ $step['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        @if($publication->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($publication->status === 'submitted')
                            <span class="badge badge-warning">Pending Approval</span>
                        @elseif($publication->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            <span class="badge badge-secondary">Draft</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Points Allocated:</strong>
                        <span class="badge badge-info">{{ number_format($publication->points_allocated ?? 0, 1) }}</span>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Publication Type:</strong> {{ ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) }}</p>
                        <p><strong>Journal Category:</strong> {{ ucfirst(str_replace('_', ' ', $publication->journal_category ?? 'N/A')) }}</p>
                        @if($publication->quartile)
                        <p><strong>Quartile:</strong> <span class="badge badge-success">{{ $publication->quartile }}</span></p>
                        @endif
                        <p><strong>Year:</strong> {{ $publication->year ?? 'N/A' }}</p>
                        @if($publication->submission_year)
                        <p><strong>Submission Year:</strong> {{ $publication->submission_year }}</p>
                        @endif
                        @if($publication->publisher)
                        <p><strong>Publisher:</strong> {{ $publication->publisher }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($publication->journal_name)
                        <p><strong>Journal:</strong> {{ $publication->journal_name }}</p>
                        @endif
                        @if($publication->conference_name)
                        <p><strong>Conference:</strong> {{ $publication->conference_name }}</p>
                        @endif
                        @if($publication->doi)
                        <p><strong>DOI:</strong> <a href="https://doi.org/{{ $publication->doi }}" target="_blank">{{ $publication->doi }}</a></p>
                        @endif
                        @if($publication->isbn)
                        <p><strong>ISBN:</strong> {{ $publication->isbn }}</p>
                        @endif
                        @if($publication->indexing_db)
                        <p><strong>Indexing DB:</strong> {{ $publication->indexing_db }}</p>
                        @endif
                        <p><strong>Sohar Affiliation:</strong>
                            @if(!is_null($publication->sohar_affiliation))
                                <span class="badge badge-{{ $publication->sohar_affiliation ? 'success' : 'secondary' }}">
                                    {{ $publication->sohar_affiliation ? 'Yes' : 'No' }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                        @if(!is_null($publication->percent_contribution))
                        <p><strong>% Contribution:</strong> {{ number_format($publication->percent_contribution, 2) }}%</p>
                        @endif
                        @if($publication->su_author_type)
                        <p><strong>SU Author Type:</strong> {{ $publication->su_author_type }}</p>
                        @endif
                        <p><strong>Student Co-author:</strong>
                            @if(!is_null($publication->student_coauthor))
                                <span class="badge badge-{{ $publication->student_coauthor ? 'success' : 'secondary' }}">
                                    {{ $publication->student_coauthor ? 'Yes' : 'No' }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                        @if($publication->student_level)
                        <p><strong>Student Level:</strong> {{ $publication->student_level }}</p>
                        @endif
                    </div>
                </div>

                @if($publication->abstract)
                <hr>
                <div>
                    <strong>Abstract:</strong>
                    <p>{{ $publication->abstract }}</p>
                </div>
                @endif

                @if($publication->published_link)
                <hr>
                <div>
                    <strong>Published Link:</strong>
                    <a href="{{ $publication->published_link }}" target="_blank">{{ $publication->published_link }}</a>
                </div>
                @endif

                @if($publication->proceedings_link)
                <hr>
                <div>
                    <strong>Proceedings Link:</strong>
                    <a href="{{ $publication->proceedings_link }}" target="_blank">{{ $publication->proceedings_link }}</a>
                </div>
                @endif

                @if($publication->workflow)
                <hr>
                <div>
                    <strong>Approval Status:</strong>
                    <p>Current Step: {{ $publication->workflow->current_step == 1 ? 'Faculty' : ($publication->workflow->current_step == 2 ? 'Coordinator' : 'Dean') }}</p>
                    @if($publication->workflow->assignee)
                        <p>Assigned to: {{ $publication->workflow->assignee->name }}</p>
                    @endif
                </div>
                @endif

                @php
                    $authors = $publication->authors ?? [];
                @endphp

                @if(is_array($authors) && count($authors) > 0)
                <hr>
                <div class="mt-3">
                    <h5>Authors</h5>
                    <div class="table-responsive mt-2">
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
                                    <td>{{ $author['name'] ?? 'N/A' }}</td>
                                    <td>{{ $author['email'] ?? '-' }}</td>
                                    <td>
                                        @if(!empty($author['is_primary']))
                                            <span class="badge badge-success">Primary</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @php
                    $evidenceFiles = $publication->evidenceFiles ?? collect();
                @endphp

                @if($evidenceFiles->count() > 0)
                <hr>
                <div class="mt-3">
                    <h5>
                        <span class="material-icons-outlined" style="font-size:18px;vertical-align:middle;">attach_file</span>
                        <span style="vertical-align: middle;">Evidence Files ({{ $evidenceFiles->count() }})</span>
                    </h5>
                    <div class="table-responsive mt-2">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>File Name</th>
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
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
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

/* Simple stepper styling for workflow progress */
.stepper {
    position: relative;
    margin-bottom: 1rem;
}

.stepper::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e5e7eb;
    z-index: 1;
}

.step-item {
    position: relative;
    z-index: 2;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.25rem;
    font-weight: 600;
    background: #f3f4f6;
    color: #6b7280;
    border: 2px solid #e5e7eb;
    transition: all 0.2s ease;
}

.step-circle.active {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
}

.step-circle.completed {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}

.step-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: #4b5563;
}
</style>
@endsection

