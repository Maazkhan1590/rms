<?php

namespace App\Http\Controllers;

use App\Models\RtnSubmission;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class RtnSubmissionController extends Controller
{
    protected WorkflowService $workflowService;
    protected LoggingService $loggingService;
    protected FileUploadService $fileUploadService;

    public function __construct(WorkflowService $workflowService, LoggingService $loggingService, FileUploadService $fileUploadService)
    {
        $this->workflowService = $workflowService;
        $this->loggingService = $loggingService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of RTN submissions
     */
    public function index(Request $request)
    {
        $query = RtnSubmission::with(['user'])
            ->where('status', 'approved');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by RTN type
        if ($request->has('rtn_type') && $request->rtn_type) {
            $query->where('rtn_type', $request->rtn_type);
        }

        // Handle sorting
        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('year')->oldest('created_at');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('year', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        // For initial load, get first 12 RTN submissions
        $totalCount = $query->count();
        $rtnSubmissions = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        return view('rtn-submissions.index', compact('rtnSubmissions', 'hasMore'));
    }

    /**
     * Load more RTN submissions via AJAX
     */
    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = 12;

        $query = RtnSubmission::with(['user'])
            ->where('status', 'approved');

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        if ($request->has('rtn_type') && $request->rtn_type) {
            $query->where('rtn_type', $request->rtn_type);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('year')->oldest('created_at');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('year', 'desc')->orderBy('created_at', 'desc');
                break;
        }

        $totalCount = $query->count();
        $rtnSubmissions = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('rtn-submissions.partials.rtn-card', ['rtnSubmissions' => $rtnSubmissions])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    /**
     * Show RTN submission form
     */
    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit RTN submissions.');
        }

        return view('rtn-submissions.create');
    }

    /**
     * Store RTN submission
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit RTN submissions.');
        }

        $user = auth()->user();
        
        $validated = $request->validate([
            'rtn_type' => 'required|in:RTN-3,RTN-4',
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'evidence_description' => 'nullable|string|max:1000',
            'units' => 'nullable|integer|min:0',
            'amount_omr' => 'nullable|numeric|min:0',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
        ]);

        $rtn = RtnSubmission::create([
            'rtn_type' => $validated['rtn_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'year' => $validated['year'],
            'submission_year' => date('Y'),
            'user_id' => auth()->id(),
            'status' => 'draft',
            'evidence_description' => $validated['evidence_description'] ?? null,
            'units' => $validated['units'] ?? null,
            'amount_omr' => $validated['amount_omr'] ?? null,
            'faculty' => $user->college->name ?? null,
            'submitted_at' => now(),
        ]);

        // Handle evidence file uploads
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'rtn',
                    $rtn->id,
                    auth()->id(),
                    'other'
                );
            }
        }

        // Handle evidence URLs
        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'rtn',
                        'submission_id' => $rtn->id,
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
        }

        // Create workflow
        $this->workflowService->createWorkflow('rtn', $rtn->id, auth()->user());

        // Log activity
        $this->loggingService->logActivity(
            'rtn_submission',
            "Submitted RTN: {$rtn->title} ({$rtn->rtn_type})",
            RtnSubmission::class,
            $rtn->id
        );

        // Log audit
        $this->loggingService->logAudit(
            'rtn.created',
            $rtn,
            null,
            $rtn->toArray()
        );

        return redirect()->route('rtn-submissions.show', $rtn->id)
            ->with('success', 'RTN submission created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    /**
     * Show RTN submission details
     */
    public function show($id)
    {
        $rtn = RtnSubmission::with(['user', 'workflow', 'evidenceFiles.uploader'])->findOrFail($id);
        
        // Allow public viewing for approved RTN, or if user owns it, or if admin/coordinator/dean
        if ($rtn->status !== 'approved' && auth()->check() && $rtn->user_id !== auth()->id() && !auth()->user()->hasAnyRole(['Admin', 'Dean', 'Coordinator'])) {
            return redirect()->route('welcome')->with('error', 'You are not authorized to view this RTN submission.');
        }

        // If AJAX request, return JSON for modal
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'rtn' => [
                    'id' => $rtn->id,
                    'title' => $rtn->title,
                    'description' => $rtn->description,
                    'rtn_type' => $rtn->rtn_type,
                    'year' => $rtn->year,
                    'amount_omr' => $rtn->amount_omr,
                    'user' => $rtn->user ? [
                        'name' => $rtn->user->name,
                    ] : null,
                ],
            ]);
        }

        return view('rtn-submissions.show', compact('rtn'));
    }

    /**
     * Submit RTN for approval
     */
    public function submit(Request $request, RtnSubmission $rtn)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit RTN submissions.');
        }

        if ($rtn->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this RTN submission.');
        }

        if ($rtn->status !== 'draft') {
            return redirect()->back()->with('error', 'RTN submission is already submitted or approved.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'rtn')
            ->where('submission_id', $rtn->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('rtn', $rtn->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);
        $workflow->refresh();

        // Update RTN status based on workflow status
        $rtn->update([
            'status' => $workflow->status == 'pending_coordinator' ? 'pending_coordinator' : 
                       ($workflow->status == 'pending_dean' ? 'pending_dean' : 'submitted'),
            'submitted_at' => now(),
        ]);

        // Log activity
        $this->loggingService->logActivity(
            'rtn_submitted',
            "Submitted RTN for approval: {$rtn->title}",
            RtnSubmission::class,
            $rtn->id
        );

        return redirect()->route('rtn-submissions.show', $rtn->id)
            ->with('success', 'RTN submission submitted for approval successfully!');
    }
}
