<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicationController extends Controller
{
    protected WorkflowService $workflowService;
    protected FileUploadService $fileUploadService;

    public function __construct(WorkflowService $workflowService, FileUploadService $fileUploadService)
    {
        $this->workflowService = $workflowService;
        $this->fileUploadService = $fileUploadService;
    }
    /**
     * Display all publications on home page
     */
    public function index(Request $request)
    {
        $query = Publication::with(['submitter', 'primaryAuthor'])
            ->where('status', 'approved');

        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('journal_name', 'like', "%{$search}%")
                  ->orWhere('conference_name', 'like', "%{$search}%");
            });
        }

        // Handle filter by type
        if ($request->has('type') && $request->type) {
            $query->where('publication_type', $request->type);
        }

        // Handle filter by year
        if ($request->has('year') && $request->year) {
            $query->where('publication_year', $request->year);
        }

        // Handle sorting
        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('published_at');
                break;
        }

        // For initial load, get first 12 publications
        $totalCount = $query->count();
        $publications = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'publications' => $publications->map(function($pub) {
                    return [
                        'id' => $pub->id,
                        'title' => $pub->title,
                        'abstract' => Str::limit(strip_tags($pub->abstract ?? ''), 200),
                        'publication_type' => $pub->publication_type,
                        'publication_year' => $pub->publication_year,
                        'published_at' => $pub->published_at ? $pub->published_at->format('F d, Y') : null,
                        'submitter_name' => $pub->submitter->name ?? 'Anonymous',
                        'primary_author_name' => $pub->primaryAuthor->name ?? null,
                        'url' => route('publications.show', $pub->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('publications.index', compact('publications', 'hasMore'));
    }

    /**
     * Load more publications via AJAX
     */
    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = 12;

        $query = Publication::with(['submitter', 'primaryAuthor'])
            ->where('status', 'approved');

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('journal_name', 'like', "%{$search}%")
                  ->orWhere('conference_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('publication_type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('publication_year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('published_at');
                break;
        }

        $totalCount = $query->count();
        $publications = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('publications.partials.publication-card', ['publications' => $publications])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    /**
     * Show publication details
     */
    public function show($id)
    {
        $publication = Publication::with(['submitter', 'primaryAuthor', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        // If AJAX request, return JSON for modal
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'publication' => [
                    'id' => $publication->id,
                    'title' => $publication->title,
                    'abstract' => $publication->abstract,
                    'publication_type' => $publication->publication_type,
                    'publication_year' => $publication->publication_year ?? $publication->year,
                    'journal_name' => $publication->journal_name,
                    'conference_name' => $publication->conference_name,
                    'doi' => $publication->doi,
                    'publisher' => $publication->publisher,
                    'isbn' => $publication->isbn,
                    'published_link' => $publication->published_link,
                    'proceedings_link' => $publication->proceedings_link,
                    'authors' => $publication->authors,
                    'submitter' => $publication->submitter ? [
                        'name' => $publication->submitter->name
                    ] : null,
                    'primaryAuthor' => $publication->primaryAuthor ? [
                        'name' => $publication->primaryAuthor->name
                    ] : null,
                ],
                'html' => view('publications.partials.modal-content', compact('publication'))->render()
            ]);
        }

        return view('publications.show', compact('publication'));
    }

    /**
     * Show publication submission form (stepper)
     * Only accessible to authenticated users with Faculty role
     */
    public function create()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit publications.');
        }

        // Check if user has Faculty role
        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit publications.');
        }

        return view('publications.create');
    }

    /**
     * Store publication submission
     * Only accessible to authenticated users with Faculty role
     */
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit publications.');
        }

        // Check if user has Faculty role
        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit publications.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'abstract' => 'nullable|string',
            'publication_type' => 'required|in:journal,conference,book,book_chapter,patent,other',
            'journal_name' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
            'doi' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'authors' => 'required|array|min:1',
            'authors.*.name' => 'required|string|max:255',
            'authors.*.email' => 'nullable|email|max:255',
            'authors.*.is_primary' => 'boolean',
            'published_link' => 'nullable|url|max:500',
            'proceedings_link' => 'nullable|url|max:500',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:10240', // 10MB max per file
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
        ]);

        // User is authenticated and has Faculty role (already checked above)
        $submittedBy = auth()->id();

        $publication = Publication::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'abstract' => $validated['abstract'] ?? null,
            'publication_type' => $validated['publication_type'],
            'journal_name' => $validated['journal_name'] ?? null,
            'conference_name' => $validated['conference_name'] ?? null,
            'publisher' => $validated['publisher'] ?? null,
            'publication_year' => $validated['publication_year'],
            'year' => $validated['publication_year'],
            'submission_year' => date('Y'),
            'doi' => $validated['doi'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'published_link' => $validated['published_link'] ?? null,
            'proceedings_link' => $validated['proceedings_link'] ?? null,
            'submitted_by' => $submittedBy,
            'status' => 'draft', // Faculty submit as draft initially
            'authors' => $validated['authors'],
            'primary_author_id' => $submittedBy,
            'submitted_at' => now(),
        ]);

        // Handle evidence file uploads
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'publication',
                    $publication->id,
                    auth()->id(),
                    'other'
                );
            }
            $publication->update(['evidence_uploaded' => true]);
        }

        // Handle evidence URLs
        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'publication',
                        'submission_id' => $publication->id,
                        'file_path' => $url,
                        'file_name' => 'URL: ' . $url,
                        'file_type' => 'text/url',
                        'file_size' => 0,
                        'file_category' => 'other',
                        'uploaded_by' => auth()->id(),
                        'uploaded_at' => now(),
                    ]);
                }
            }
            if (!empty(array_filter($request->evidence_urls))) {
                $publication->update(['evidence_uploaded' => true]);
            }
        }

        // Create workflow for the publication (in draft status)
        $this->workflowService->createWorkflow('publication', $publication->id, auth()->user());

        return redirect()->route('publications.show', $publication->id)
            ->with('success', 'Publication created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    /**
     * Submit publication for approval (creates/submits workflow)
     */
    public function submit(Request $request, Publication $publication)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit publications.');
        }

        // Check if user owns this publication
        if ($publication->submitted_by !== auth()->id() && $publication->primary_author_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this publication.');
        }

        // Check if publication is in draft status
        if ($publication->status !== 'draft') {
            return redirect()->back()->with('error', 'Publication is already submitted or approved.');
        }

        // Find or create workflow
        $workflow = ApprovalWorkflow::where('submission_type', 'publication')
            ->where('submission_id', $publication->id)
            ->first();

        if (!$workflow) {
            // Create workflow if it doesn't exist
            $workflow = $this->workflowService->createWorkflow('publication', $publication->id, auth()->user());
        }

        // Submit workflow for approval
        $workflow = $this->workflowService->submitWorkflow($workflow);
        $workflow->refresh();

        // Update publication status based on workflow status
        $publication->update([
            'status' => $workflow->status == 'pending_coordinator' ? 'pending_coordinator' : 
                       ($workflow->status == 'pending_dean' ? 'pending_dean' : 'submitted'),
            'submitted_at' => now(),
        ]);

        return redirect()->route('publications.show', $publication->id)
            ->with('success', 'Publication submitted for approval successfully! It will be reviewed by coordinators and deans.');
    }
}
