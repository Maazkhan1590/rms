<?php

namespace App\Http\Controllers;

use App\Models\Commercialization;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class CommercializationController extends Controller
{
    protected WorkflowService $workflowService;
    protected FileUploadService $fileUploadService;

    public function __construct(WorkflowService $workflowService, FileUploadService $fileUploadService)
    {
        $this->workflowService = $workflowService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display all commercializations
     */
    public function index(Request $request)
    {
        $query = Commercialization::with(['submitter', 'ownerTeam'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_service_name', 'like', "%{$search}%")
                  ->orWhere('client_market', 'like', "%{$search}%");
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
                $query->oldest('launch_date');
                break;
            case 'name':
                $query->orderBy('product_service_name', 'asc');
                break;
            default:
                $query->latest('launch_date');
                break;
        }

        $totalCount = $query->count();
        $commercializations = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'commercializations' => $commercializations->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_service_name' => $item->product_service_name,
                        'type' => $item->type,
                        'stage' => $item->stage,
                        'launch_date' => $item->launch_date ? $item->launch_date->format('F d, Y') : null,
                        'url' => route('commercializations.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('commercializations.index', compact('commercializations', 'hasMore'));
    }

    /**
     * Load more commercializations via AJAX
     */
    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = Commercialization::with(['submitter', 'ownerTeam'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_service_name', 'like', "%{$search}%");
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
                $query->oldest('launch_date');
                break;
            case 'name':
                $query->orderBy('product_service_name', 'asc');
                break;
            default:
                $query->latest('launch_date');
                break;
        }

        $totalCount = $query->count();
        $commercializations = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('commercializations.partials.commercialization-card', ['commercializations' => $commercializations])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    /**
     * Show commercialization details
     */
    public function show($id)
    {
        $commercialization = Commercialization::with(['submitter', 'ownerTeam', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('commercializations.show', compact('commercialization'));
    }

    /**
     * Show commercialization submission form
     */
    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit commercializations.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit commercializations.');
        }

        return view('commercializations.create');
    }

    /**
     * Store commercialization submission
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit commercializations.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit commercializations.');
        }

        $validated = $request->validate([
            'product_service_name' => 'required|string|max:255',
            'type' => 'required|in:product,service',
            'stage' => 'nullable|in:prototype,pilot,launched',
            'launch_date' => 'nullable|date',
            'revenue_omr' => 'nullable|numeric|min:0',
            'ip_patent' => 'boolean',
            'client_market' => 'nullable|string|max:255',
            'evidence_link' => 'nullable|url|max:500',
            'sdg_s' => 'nullable|string|max:255',
            'reporting_period' => 'nullable|in:q1,q2,q3,q4',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $commercialization = Commercialization::create([
            'product_service_name' => $validated['product_service_name'],
            'type' => $validated['type'],
            'stage' => $validated['stage'] ?? null,
            'launch_date' => $validated['launch_date'] ?? null,
            'revenue_omr' => $validated['revenue_omr'] ?? null,
            'ip_patent' => $request->has('ip_patent') ? true : false,
            'client_market' => $validated['client_market'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'sdg_s' => $validated['sdg_s'] ?? null,
            'reporting_period' => $validated['reporting_period'] ?? null,
            'year' => $validated['year'] ?? ($validated['launch_date'] ? date('Y', strtotime($validated['launch_date'])) : date('Y')),
            'owner_team_id' => auth()->id(),
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
                    'commercialization',
                    $commercialization->id,
                    auth()->id(),
                    'other'
                );
            }
            $commercialization->update(['evidence_uploaded' => true]);
        }

        // Handle evidence URLs
        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'commercialization',
                        'submission_id' => $commercialization->id,
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
                $commercialization->update(['evidence_uploaded' => true]);
            }
        }

        // Create workflow
        $this->workflowService->createWorkflow('commercialization', $commercialization->id, auth()->user());

        return redirect()->route('commercializations.show', $commercialization->id)
            ->with('success', 'Commercialization created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    /**
     * Submit commercialization for approval
     */
    public function submit(Request $request, Commercialization $commercialization)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit commercializations.');
        }

        if ($commercialization->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this commercialization.');
        }

        if ($commercialization->status !== 'draft') {
            return redirect()->back()->with('error', 'This commercialization has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'commercialization')
            ->where('submission_id', $commercialization->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('commercialization', $commercialization->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $commercialization->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('commercializations.show', $commercialization->id)
            ->with('success', 'Commercialization submitted for approval successfully!');
    }
}
