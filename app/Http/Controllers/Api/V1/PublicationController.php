<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponser;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\Publication;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicationController extends Controller
{
    use ApiResponser;

    protected ScoringService $scoringService;
    protected WorkflowService $workflowService;

    public function __construct(ScoringService $scoringService, WorkflowService $workflowService)
    {
        $this->scoringService = $scoringService;
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of publications
     */
    public function index(Request $request): JsonResponse
    {
        $query = Publication::with(['submitter', 'primaryAuthor', 'policyVersion', 'workflow']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('publication_type')) {
            $query->where('publication_type', $request->publication_type);
        }

        if ($request->has('user_id')) {
            $query->where('primary_author_id', $request->user_id);
        }

        $publications = $query->latest()->paginate($request->get('per_page', 15));

        return $this->successResponse($publications, 'Publications retrieved successfully');
    }

    /**
     * Store a newly created publication
     */
    public function store(StorePublicationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['submitted_by'] = auth()->id();
        $data['primary_author_id'] = auth()->id();
        $data['slug'] = \Str::slug($data['title']);

        $publication = Publication::create($data);

        // Calculate points
        $this->scoringService->calculatePublicationPoints($publication);

        // Create workflow if not exists
        if (!$publication->workflow) {
            $this->workflowService->createWorkflow('publication', $publication->id, auth()->user());
        }

        return $this->successResponse($publication->load(['submitter', 'primaryAuthor', 'workflow']), 'Publication created successfully', 201);
    }

    /**
     * Display the specified publication
     */
    public function show(Publication $publication): JsonResponse
    {
        $publication->load(['submitter', 'primaryAuthor', 'policyVersion', 'workflow', 'evidenceFiles']);
        return $this->successResponse($publication, 'Publication retrieved successfully');
    }

    /**
     * Update the specified publication
     */
    public function update(UpdatePublicationRequest $request, Publication $publication): JsonResponse
    {
        // Check if publication can be edited
        if ($publication->status === 'approved' && $publication->points_locked) {
            return $this->errorResponse('Cannot edit approved publication with locked points', 403);
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

        return $this->successResponse($publication->fresh()->load(['submitter', 'primaryAuthor', 'workflow']), 'Publication updated successfully');
    }

    /**
     * Remove the specified publication
     */
    public function destroy(Publication $publication): JsonResponse
    {
        if ($publication->status === 'approved') {
            return $this->errorResponse('Cannot delete approved publication', 403);
        }

        $publication->delete();

        return $this->successResponse(null, 'Publication deleted successfully');
    }

    /**
     * Submit publication for approval
     */
    public function submit(Publication $publication): JsonResponse
    {
        if ($publication->submitted_by !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($publication->status !== 'draft') {
            return $this->errorResponse('Publication already submitted', 400);
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'publication')
            ->where('submission_id', $publication->id)
            ->first();
            
        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('publication', $publication->id, auth()->user());
        }
        
        $workflow = $this->workflowService->submitWorkflow($workflow);

        $publication->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $this->successResponse($publication->load('workflow'), 'Publication submitted for approval');
    }
}

