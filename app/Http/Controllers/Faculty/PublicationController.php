<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\Publication;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    protected ScoringService $scoringService;
    protected WorkflowService $workflowService;

    public function __construct(ScoringService $scoringService, WorkflowService $workflowService)
    {
        $this->middleware('auth');
        $this->scoringService = $scoringService;
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of the user's publications
     */
    public function index(Request $request)
    {
        $publications = Publication::where('primary_author_id', auth()->id())
            ->with(['workflow', 'policyVersion'])
            ->when($request->has('status'), function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->has('year'), function($query) use ($request) {
                $query->where('year', $request->year);
            })
            ->latest()
            ->paginate(15);

        return view('faculty.publications.index', compact('publications'));
    }

    /**
     * Display all publications (for viewing)
     */
    public function all(Request $request)
    {
        $publications = Publication::with(['primaryAuthor', 'submitter', 'workflow', 'policyVersion'])
            ->when($request->has('status'), function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->has('year'), function($query) use ($request) {
                $query->where('year', $request->year);
            })
            ->when($request->has('search'), function($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('faculty.publications.all', compact('publications'));
    }

    /**
     * Show the form for creating a new publication
     */
    public function create()
    {
        return view('faculty.publications.create');
    }

    /**
     * Store a newly created publication
     */
    public function store(StorePublicationRequest $request)
    {
        $data = $request->validated();
        $data['submitted_by'] = auth()->id();
        $data['primary_author_id'] = auth()->id();
        $data['slug'] = \Str::slug($data['title']);
        $data['status'] = 'draft';
        $data['college'] = auth()->user()->college->name ?? null;
        $data['department'] = auth()->user()->department->name ?? null;

        $publication = Publication::create($data);

        // Calculate points
        $this->scoringService->calculatePublicationPoints($publication);

        // Create workflow
        $this->workflowService->createWorkflow('publication', $publication->id, auth()->user());

        return redirect()->route('faculty.publications.index')
            ->with('success', 'Publication created successfully. You can submit it for approval when ready.');
    }

    /**
     * Display the specified publication
     */
    public function show(Publication $publication)
    {
        // Check authorization
        if ($publication->primary_author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $publication->load(['workflow.history.performer', 'evidenceFiles', 'policyVersion']);

        return view('faculty.publications.show', compact('publication'));
    }

    /**
     * Show the form for editing the specified publication
     */
    public function edit(Publication $publication)
    {
        // Check authorization
        if ($publication->primary_author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($publication->status === 'approved' && $publication->points_locked) {
            return redirect()->route('faculty.publications.show', $publication)
                ->with('error', 'Cannot edit approved publication with locked points');
        }

        return view('faculty.publications.edit', compact('publication'));
    }

    /**
     * Update the specified publication
     */
    public function update(UpdatePublicationRequest $request, Publication $publication)
    {
        // Check authorization
        if ($publication->primary_author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($publication->status === 'approved' && $publication->points_locked) {
            return redirect()->route('faculty.publications.show', $publication)
                ->with('error', 'Cannot edit approved publication with locked points');
        }

        $data = $request->validated();
        
        if (isset($data['title']) && $data['title'] !== $publication->title) {
            $data['slug'] = \Str::slug($data['title']);
        }

        $publication->update($data);

        // Recalculate points if relevant fields changed
        if (isset($data['publication_type']) || isset($data['journal_category']) || isset($data['quartile'])) {
            $this->scoringService->calculatePublicationPoints($publication);
        }

        return redirect()->route('faculty.publications.show', $publication)
            ->with('success', 'Publication updated successfully');
    }

    /**
     * Submit publication for approval
     */
    public function submit(Publication $publication)
    {
        // Check authorization
        if ($publication->primary_author_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($publication->status !== 'draft') {
            return redirect()->route('faculty.publications.show', $publication)
                ->with('error', 'Publication already submitted');
        }

        $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'publication')
            ->where('submission_id', $publication->id)
            ->first();
            
        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('publication', $publication->id, auth()->user());
        }
        
        $this->workflowService->submitWorkflow($workflow);

        $publication->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('faculty.publications.show', $publication)
            ->with('success', 'Publication submitted for approval');
    }
}

