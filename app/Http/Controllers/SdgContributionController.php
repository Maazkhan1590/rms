<?php

namespace App\Http\Controllers;

use App\Models\SdgContribution;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Support\ValidationRules;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class SdgContributionController extends Controller
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
        $query = SdgContribution::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->has('sdg') && $request->sdg) {
            $query->where('sdg', $request->sdg);
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
                $query->oldest('date');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $contributions = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'contributions' => $contributions->map(function($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'sdg' => $item->sdg,
                        'type' => $item->type,
                        'date' => $item->date ? $item->date->format('F d, Y') : null,
                        'url' => route('sdg-contributions.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('sdg-contributions.index', compact('contributions', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = SdgContribution::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->has('sdg') && $request->sdg) {
            $query->where('sdg', $request->sdg);
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
                $query->oldest('date');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $contributions = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('sdg-contributions.partials.contribution-card', ['contributions' => $contributions])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $contribution = SdgContribution::with([
            'submitter',
            'user',
            'approver',
            'evidenceFiles',
            'workflow.history.performer',
            'workflow.submitter',
            'workflow.assignee',
        ])->findOrFail($id);

        return view('sdg-contributions.show', compact('contribution'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit SDG contributions.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit SDG contributions.');
        }

        return view('sdg-contributions.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit SDG contributions.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit SDG contributions.');
        }

        $validated = $request->validate([
            'title' => ValidationRules::title(required: true, max: 500),
            'type' => 'nullable|in:paper,project,talk,other',
            'sdg' => 'required|integer|min:1|max:17',
            'date' => 'nullable|date',
            'evidence_link' => ValidationRules::url(required: false, max: 500),
            'related_type' => ValidationRules::organization(required: false, max: 255),
            'related_id' => 'nullable|integer',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => ValidationRules::url(required: false, max: 500),
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $contribution = SdgContribution::create([
            'title' => $validated['title'],
            'type' => $validated['type'] ?? null,
            'sdg' => $validated['sdg'],
            'date' => $validated['date'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'related_type' => $validated['related_type'] ?? null,
            'related_id' => $validated['related_id'] ?? null,
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
                    'sdg_contribution',
                    $contribution->id,
                    auth()->id(),
                    'other'
                );
            }
            $contribution->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'sdg_contribution',
                        'submission_id' => $contribution->id,
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
                $contribution->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('sdg_contribution', $contribution->id, auth()->user());

        return redirect()->route('sdg-contributions.show', $contribution->id)
            ->with('success', 'SDG contribution created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, SdgContribution $contribution)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit SDG contributions.');
        }

        if ($contribution->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this SDG contribution.');
        }

        if ($contribution->status !== 'draft') {
            return redirect()->back()->with('error', 'This SDG contribution has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'sdg_contribution')
            ->where('submission_id', $contribution->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('sdg_contribution', $contribution->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $contribution->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('sdg-contributions.show', $contribution->id)
            ->with('success', 'SDG contribution submitted for approval successfully!');
    }
}
