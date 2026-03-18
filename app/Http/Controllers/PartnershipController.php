<?php

namespace App\Http\Controllers;

use App\Models\PartnershipMou;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Support\ValidationRules;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    protected WorkflowService $workflowService;
    protected FileUploadService $fileUploadService;

    public function __construct(WorkflowService $workflowService, FileUploadService $fileUploadService)
    {
        $this->workflowService = $workflowService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display all partnerships/MOUs on home page
     */
    public function index(Request $request)
    {
        $query = PartnershipMou::with(['submitter', 'leadStaff'])
            ->where('status', 'approved');

        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('partner_organization', 'like', "%{$search}%")
                  ->orWhere('scope_theme', 'like', "%{$search}%")
                  ->orWhere('outputs_papers_grants_events', 'like', "%{$search}%");
            });
        }

        // Handle filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Handle filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Handle sorting
        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date_signed');
                break;
            case 'organization':
                $query->orderBy('partner_organization', 'asc');
                break;
            case 'newest':
            default:
                $query->latest('date_signed');
                break;
        }

        // For initial load, get first 12 partnerships
        $totalCount = $query->count();
        $partnerships = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'partnerships' => $partnerships->map(function($partnership) {
                    return [
                        'id' => $partnership->id,
                        'partner_organization' => $partnership->partner_organization,
                        'type' => $partnership->type,
                        'date_signed' => $partnership->date_signed ? $partnership->date_signed->format('F d, Y') : null,
                        'scope_theme' => \Str::limit($partnership->scope_theme ?? '', 200),
                        'url' => route('partnerships.show', $partnership->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('partnerships.index', compact('partnerships', 'hasMore'));
    }

    /**
     * Load more partnerships via AJAX
     */
    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = PartnershipMou::with(['submitter', 'leadStaff'])
            ->where('status', 'approved');

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('partner_organization', 'like', "%{$search}%")
                  ->orWhere('scope_theme', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date_signed');
                break;
            case 'organization':
                $query->orderBy('partner_organization', 'asc');
                break;
            default:
                $query->latest('date_signed');
                break;
        }

        $totalCount = $query->count();
        $partnerships = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('partnerships.partials.partnership-card', ['partnerships' => $partnerships])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    /**
     * Show partnership details
     */
    public function show($id)
    {
        $partnership = PartnershipMou::with([
            'submitter',
            'leadStaff',
            'approver',
            'evidenceFiles',
            'workflow.history.performer',
            'workflow.submitter',
            'workflow.assignee',
        ])->findOrFail($id);

        return view('partnerships.show', compact('partnership'));
    }

    /**
     * Show partnership submission form
     */
    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit partnerships/MOUs.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit partnerships/MOUs.');
        }

        return view('partnerships.create');
    }

    /**
     * Store partnership submission
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit partnerships/MOUs.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit partnerships/MOUs.');
        }

        $validated = $request->validate([
            'partner_organization' => ValidationRules::organization(required: true, max: 255),
            'type' => 'required|in:mou,moa,project,industry',
            'date_signed' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:date_signed',
            'scope_theme' => 'nullable|string',
            'lead_staff_id' => 'nullable|exists:users,id',
            'outputs_papers_grants_events' => 'nullable|string',
            'evidence_link' => ValidationRules::url(required: false, max: 500),
            'sdg_s' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => ValidationRules::url(required: false, max: 500),
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $partnership = PartnershipMou::create([
            'partner_organization' => $validated['partner_organization'],
            'type' => $validated['type'],
            'date_signed' => $validated['date_signed'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'scope_theme' => $validated['scope_theme'] ?? null,
            'lead_staff_id' => $validated['lead_staff_id'] ?? auth()->id(),
            'outputs_papers_grants_events' => $validated['outputs_papers_grants_events'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'sdg_s' => $validated['sdg_s'] ?? null,
            'year' => $validated['year'] ?? ($validated['date_signed'] ? date('Y', strtotime($validated['date_signed'])) : date('Y')),
            'submitted_by' => auth()->id(),
            'status' => 'draft',
            'evidence_description' => $validated['evidence_description'] ?? null,
            'evidence_required' => true,
            'submitted_at' => now(),
        ]);

        // Handle evidence file uploads
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'mou',
                    $partnership->id,
                    auth()->id(),
                    'other'
                );
            }
            $partnership->update(['evidence_uploaded' => true]);
        }

        // Handle evidence URLs
        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'mou',
                        'submission_id' => $partnership->id,
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
                $partnership->update(['evidence_uploaded' => true]);
            }
        }

        // Create workflow for the partnership (in draft status)
        $this->workflowService->createWorkflow('mou', $partnership->id, auth()->user());

        return redirect()->route('partnerships.show', $partnership->id)
            ->with('success', 'Partnership/MOU created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    /**
     * Submit partnership for approval
     */
    public function submit(Request $request, PartnershipMou $partnership)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit partnerships/MOUs.');
        }

        if ($partnership->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this partnership/MOU.');
        }

        if ($partnership->status !== 'draft') {
            return redirect()->back()->with('error', 'This partnership/MOU has already been submitted.');
        }

        // Find or create workflow
        $workflow = ApprovalWorkflow::where('submission_type', 'mou')
            ->where('submission_id', $partnership->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('mou', $partnership->id, auth()->user());
        }

        // Submit the workflow
        $workflow = $this->workflowService->submitWorkflow($workflow);

        // Update partnership status
        $partnership->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('partnerships.show', $partnership->id)
            ->with('success', 'Partnership/MOU submitted for approval successfully!');
    }
}
