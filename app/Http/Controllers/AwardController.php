<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class AwardController extends Controller
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
        $query = Award::with(['submitter'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('award_name', 'like', "%{$search}%")
                  ->orWhere('awarding_organization', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('award_type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('award_date');
                break;
            case 'name':
                $query->orderBy('award_name', 'asc');
                break;
            default:
                $query->latest('award_date');
                break;
        }

        $totalCount = $query->count();
        $awards = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'awards' => $awards->map(function($item) {
                    return [
                        'id' => $item->id,
                        'award_name' => $item->award_name,
                        'awarding_organization' => $item->awarding_organization,
                        'award_date' => $item->award_date ? $item->award_date->format('F d, Y') : null,
                        'url' => route('awards.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('awards.index', compact('awards', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = Award::with(['submitter'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('award_name', 'like', "%{$search}%");
        }

        if ($request->has('type') && $request->type) {
            $query->where('award_type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('award_date');
                break;
            case 'name':
                $query->orderBy('award_name', 'asc');
                break;
            default:
                $query->latest('award_date');
                break;
        }

        $totalCount = $query->count();
        $awards = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('awards.partials.award-card', ['awards' => $awards])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $award = Award::with(['submitter', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('awards.show', compact('award'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit awards.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit awards.');
        }

        return view('awards.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit awards.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit awards.');
        }

        $validated = $request->validate([
            'award_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'awarding_organization' => 'nullable|string|max:255',
            'award_date' => 'nullable|date',
            'award_type' => 'nullable|in:national,international,regional,institutional,other',
            'category' => 'nullable|string|max:255',
            'achievement_description' => 'nullable|string',
            'evidence_link' => 'nullable|url|max:500',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $award = Award::create([
            'award_name' => $validated['award_name'],
            'description' => $validated['description'] ?? null,
            'awarding_organization' => $validated['awarding_organization'] ?? null,
            'award_date' => $validated['award_date'] ?? null,
            'award_type' => $validated['award_type'] ?? null,
            'category' => $validated['category'] ?? null,
            'achievement_description' => $validated['achievement_description'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'year' => $validated['year'] ?? ($validated['award_date'] ? date('Y', strtotime($validated['award_date'])) : date('Y')),
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
                    'award',
                    $award->id,
                    auth()->id(),
                    'other'
                );
            }
            $award->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'award',
                        'submission_id' => $award->id,
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
                $award->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('award', $award->id, auth()->user());

        return redirect()->route('awards.show', $award->id)
            ->with('success', 'Award created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, Award $award)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit awards.');
        }

        if ($award->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this award.');
        }

        if ($award->status !== 'draft') {
            return redirect()->back()->with('error', 'This award has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'award')
            ->where('submission_id', $award->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('award', $award->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $award->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('awards.show', $award->id)
            ->with('success', 'Award submitted for approval successfully!');
    }
}
