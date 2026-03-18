<?php

namespace App\Http\Controllers;

use App\Models\ResearchInvestment;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class ResearchInvestmentController extends Controller
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
        $query = ResearchInvestment::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item', 'like', "%{$search}%")
                  ->orWhere('funding_source', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date');
                break;
            case 'item':
                $query->orderBy('item', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $investments = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'investments' => $investments->map(function($item) {
                    return [
                        'id' => $item->id,
                        'item' => $item->item,
                        'category' => $item->category,
                        'amount_omr' => $item->amount_omr,
                        'date' => $item->date ? $item->date->format('F d, Y') : null,
                        'url' => route('research-investments.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('research-investments.index', compact('investments', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = ResearchInvestment::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('item', 'like', "%{$search}%");
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date');
                break;
            case 'item':
                $query->orderBy('item', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $investments = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('research-investments.partials.investment-card', ['investments' => $investments])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $investment = ResearchInvestment::with([
            'submitter',
            'user',
            'approver',
            'evidenceFiles',
            'workflow.history.performer',
            'workflow.submitter',
            'workflow.assignee',
        ])->findOrFail($id);

        return view('research-investments.show', compact('investment'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit research investments.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit research investments.');
        }

        return view('research-investments.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit research investments.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit research investments.');
        }

        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'category' => 'nullable|in:equipment,software,apc,travel,training,other',
            'date' => 'nullable|date',
            'amount_omr' => 'nullable|numeric|min:0',
            'funding_source' => 'nullable|string|max:255',
            'evidence_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string',
            'reporting_period' => 'nullable|in:q1,q2,q3,q4',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $investment = ResearchInvestment::create([
            'item' => $validated['item'],
            'category' => $validated['category'] ?? null,
            'date' => $validated['date'] ?? null,
            'amount_omr' => $validated['amount_omr'] ?? null,
            'funding_source' => $validated['funding_source'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'reporting_period' => $validated['reporting_period'] ?? null,
            'year' => $validated['year'] ?? ($validated['date'] ? date('Y', strtotime($validated['date'])) : date('Y')),
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
                    'research_investment',
                    $investment->id,
                    auth()->id(),
                    'other'
                );
            }
            $investment->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'research_investment',
                        'submission_id' => $investment->id,
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
                $investment->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('research_investment', $investment->id, auth()->user());

        return redirect()->route('research-investments.show', $investment->id)
            ->with('success', 'Research investment created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, ResearchInvestment $investment)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit research investments.');
        }

        if ($investment->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this research investment.');
        }

        if ($investment->status !== 'draft') {
            return redirect()->back()->with('error', 'This research investment has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'research_investment')
            ->where('submission_id', $investment->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('research_investment', $investment->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $investment->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('research-investments.show', $investment->id)
            ->with('success', 'Research investment submitted for approval successfully!');
    }
}
