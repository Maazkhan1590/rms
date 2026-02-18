<?php

namespace App\Http\Controllers;

use App\Models\BlockFunding;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class BlockFundingController extends Controller
{
    protected WorkflowService $workflowService;
    protected FileUploadService $fileUploadService;

    public function __construct(WorkflowService $workflowService, FileUploadService $fileUploadService)
    {
        $this->workflowService = $workflowService;
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $query = BlockFunding::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_title', 'like', "%{$search}%")
                  ->orWhere('funding_source', 'like', "%{$search}%");
            });
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_date');
                break;
            case 'title':
                $query->orderBy('project_title', 'asc');
                break;
            default:
                $query->latest('start_date');
                break;
        }

        $totalCount = $query->count();
        $fundings = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'fundings' => $fundings->map(function($item) {
                    return [
                        'id' => $item->id,
                        'project_title' => $item->project_title,
                        'funding_source' => $item->funding_source,
                        'amount_omr' => $item->amount_omr,
                        'url' => route('block-fundings.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('block-fundings.index', compact('fundings', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = BlockFunding::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('project_title', 'like', "%{$search}%");
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('start_date');
                break;
            case 'title':
                $query->orderBy('project_title', 'asc');
                break;
            default:
                $query->latest('start_date');
                break;
        }

        $totalCount = $query->count();
        $fundings = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('block-fundings.partials.funding-card', ['fundings' => $fundings])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $funding = BlockFunding::with(['submitter', 'user', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('block-fundings.show', compact('funding'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit block fundings.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit block fundings.');
        }

        return view('block-fundings.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit block fundings.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit block fundings.');
        }

        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'funding_source' => 'nullable|string|max:255',
            'amount_omr' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'evidence_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $funding = BlockFunding::create([
            'project_title' => $validated['project_title'],
            'funding_source' => $validated['funding_source'] ?? null,
            'amount_omr' => $validated['amount_omr'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'year' => $validated['year'] ?? ($validated['start_date'] ? date('Y', strtotime($validated['start_date'])) : date('Y')),
            'user_id' => auth()->id(),
            'submitted_by' => auth()->id(),
            'status' => 'draft',
            'evidence_description' => $validated['evidence_description'] ?? null,
            'evidence_required' => true,
            'submitted_at' => now(),
        ]);

        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'block_funding',
                    $funding->id,
                    auth()->id(),
                    'other'
                );
            }
            $funding->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'block_funding',
                        'submission_id' => $funding->id,
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
                $funding->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('block_funding', $funding->id, auth()->user());

        return redirect()->route('block-fundings.show', $funding->id)
            ->with('success', 'Block funding created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, BlockFunding $funding)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit block fundings.');
        }

        if ($funding->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this block funding.');
        }

        if ($funding->status !== 'draft') {
            return redirect()->back()->with('error', 'This block funding has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'block_funding')
            ->where('submission_id', $funding->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('block_funding', $funding->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $funding->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('block-fundings.show', $funding->id)
            ->with('success', 'Block funding submitted for approval successfully!');
    }
}
