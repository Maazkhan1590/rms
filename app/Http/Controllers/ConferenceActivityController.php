<?php

namespace App\Http\Controllers;

use App\Models\ConferenceActivity;
use App\Models\ApprovalWorkflow;
use App\Models\EvidenceFile;
use App\Services\WorkflowService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class ConferenceActivityController extends Controller
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
        $query = ConferenceActivity::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('conference', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('activity_type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date');
                break;
            case 'conference':
                $query->orderBy('conference', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $activities = $query->take(12)->get();
        $hasMore = $totalCount > 12;

        if ($request->ajax()) {
            return response()->json([
                'activities' => $activities->map(function($item) {
                    return [
                        'id' => $item->id,
                        'conference' => $item->conference,
                        'activity_type' => $item->activity_type,
                        'country' => $item->country,
                        'date' => $item->date ? $item->date->format('F d, Y') : null,
                        'url' => route('conference-activities.show', $item->id),
                    ];
                }),
                'hasMore' => $hasMore,
            ]);
        }

        return view('conference-activities.index', compact('activities', 'hasMore'));
    }

    public function loadMore(Request $request)
    {
        $offset = $request->get('offset', 12);
        $limit = $request->get('limit', 12);

        $query = ConferenceActivity::with(['submitter', 'user'])
            ->where('status', 'approved');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('conference', 'like', "%{$search}%");
        }

        if ($request->has('type') && $request->type) {
            $query->where('activity_type', $request->type);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'oldest':
                $query->oldest('date');
                break;
            case 'conference':
                $query->orderBy('conference', 'asc');
                break;
            default:
                $query->latest('date');
                break;
        }

        $totalCount = $query->count();
        $activities = $query->skip($offset)->take($limit)->get();
        $hasMore = ($offset + $limit) < $totalCount;

        return response()->json([
            'html' => view('conference-activities.partials.activity-card', ['activities' => $activities])->render(),
            'hasMore' => $hasMore,
        ]);
    }

    public function show($id)
    {
        $activity = ConferenceActivity::with(['submitter', 'user', 'approver', 'evidenceFiles'])
            ->findOrFail($id);

        return view('conference-activities.show', compact('activity'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit conference activities.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit conference activities.');
        }

        return view('conference-activities.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit conference activities.');
        }

        if (!auth()->user()->hasRole('Faculty')) {
            return redirect()->route('welcome')->with('error', 'Please login with a valid account to submit conference activities.');
        }

        $validated = $request->validate([
            'conference' => 'required|string|max:255',
            'activity_type' => 'nullable|in:keynote,invited,regular,plenary',
            'country' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'evidence_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,gif|max:10240',
            'evidence_urls' => 'nullable|array',
            'evidence_urls.*' => 'nullable|url|max:500',
            'evidence_description' => 'nullable|string|max:1000',
        ]);

        $activity = ConferenceActivity::create([
            'conference' => $validated['conference'],
            'activity_type' => $validated['activity_type'] ?? null,
            'country' => $validated['country'] ?? null,
            'date' => $validated['date'] ?? null,
            'evidence_link' => $validated['evidence_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
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
                    'conference_activity',
                    $activity->id,
                    auth()->id(),
                    'other'
                );
            }
            $activity->update(['evidence_uploaded' => true]);
        }

        if ($request->has('evidence_urls') && is_array($request->evidence_urls)) {
            foreach ($request->evidence_urls as $url) {
                if (!empty($url)) {
                    EvidenceFile::create([
                        'submission_type' => 'conference_activity',
                        'submission_id' => $activity->id,
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
                $activity->update(['evidence_uploaded' => true]);
            }
        }

        $this->workflowService->createWorkflow('conference_activity', $activity->id, auth()->user());

        return redirect()->route('conference-activities.show', $activity->id)
            ->with('success', 'Conference activity created successfully! It is currently in draft status. You can submit it for approval later.');
    }

    public function submit(Request $request, ConferenceActivity $activity)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit conference activities.');
        }

        if ($activity->submitted_by !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to submit this conference activity.');
        }

        if ($activity->status !== 'draft') {
            return redirect()->back()->with('error', 'This conference activity has already been submitted.');
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'conference_activity')
            ->where('submission_id', $activity->id)
            ->first();

        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('conference_activity', $activity->id, auth()->user());
        }

        $workflow = $this->workflowService->submitWorkflow($workflow);

        $activity->update([
            'status' => $workflow->status === 'pending_coordinator' ? 'pending_coordinator' : 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('conference-activities.show', $activity->id)
            ->with('success', 'Conference activity submitted for approval successfully!');
    }
}
