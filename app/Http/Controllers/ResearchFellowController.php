<?php

namespace App\Http\Controllers;

use App\Models\ResearchFellow;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class ResearchFellowController extends Controller
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
        $query = ResearchFellow::with(['submitter', 'user'])
            ->where('workflow_status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('publication_title', 'like', "%{$search}%")
                  ->orWhere('journal', 'like', "%{$search}%");
            });
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('year');
                break;
            case 'title':
                $query->orderBy('publication_title', 'asc');
                break;
            default:
                $query->latest('year');
                break;
        }

        $totalCount = $query->count();
        $fellows = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'fellows' => $fellows->map(function($item) {
                    return [
                        'id' => $item->id,
                        'publication_title' => $item->publication_title,
                        'journal' => $item->journal,
                        'year' => $item->year,
                        'url' => route('research-fellows.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('research-fellows.index', compact('fellows', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = ResearchFellow::with(['submitter', 'user'])
            ->where('workflow_status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('publication_title', 'like', "%{$search}%");
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('year');
                break;
            case 'title':
                $query->orderBy('publication_title', 'asc');
                break;
            default:
                $query->latest('year');
                break;
        }

        $totalCount = $query->count();
        $fellows = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('research-fellows.partials.fellow-card', ['fellows' => $fellows])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $fellow = ResearchFellow::with([
            'submitter',
            'user',
            'approver',
            'evidenceFiles',
            'workflow.history.performer',
            'workflow.submitter',
            'workflow.assignee',
        ])->findOrFail($id);

        return view('research-fellows.show', compact('fellow'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit research fellows.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit research fellows.');
        }

        return view('research-fellows.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit research fellows.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit research fellows.');
        }

        $validated = $request->validate([
            'publication_title' => 'required|string|max:500',
            'journal' => 'nullable|string|max:255',
            'doi' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'indexed' => 'boolean',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'count_for_urc' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'evidence_link' => 'nullable|url|max:500',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $fellow = ResearchFellow::create([
            'publication_title' => $validated['publication_title'],
            'journal' => $validated['journal'] ?? null,
            'doi' => $validated['doi'] ?? null,
            'status' => $validated['status'] ?? null,
            'indexed' => $request->has('indexed') ? true : false,
            'year' => $validated['year'] ?? date('Y'),
            'count_for_urc' => $validated['count_for_urc'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'user_id' => auth()->id(),
            'submitted_by' => auth()->id(),
            'workflow_status' => 'draft',
            'evidence_description' => $validated['evidence_description'] ?? null,
            'evidence_required' => true,
            'submitted_at' => now(),
        ]);

        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $this->fileUploadService->uploadEvidenceFile(
                    $file,
                    'research_fellow',
                    $fellow->id,
                    auth()->id(),
                    'other'
                );
            }
            $fellow->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'research_fellow',
                        'submission_id' => $fellow->id,
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
                $fellow->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('research_fellow', $fellow->id, auth()->user());

        return redirect()->route('research-fellows.show', $fellow->id)
            ->with('success', 'Research fellow created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, ResearchFellow $fellow)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit research fellows.');
        }

        if ($fellow->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this research fellow.');
        }

        if ($fellow->workflow_status !== 'draft') {
            return redirect()->back()->with('error', 'This research fellow has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'research_fellow')
            ->where('submission_id', $fellow->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('research_fellow', $fellow->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $fellow->update([
            'workflow_status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('research-fellows.show', $fellow->id)
            ->with('success', 'Research fellow submitted for approval successfully!');
    }
}
