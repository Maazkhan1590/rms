<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponser;
use App\Http\Requests\StoreGrantRequest;
use App\Http\Requests\UpdateGrantRequest;
use App\Models\Grant;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GrantController extends Controller
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
     * Display a listing of grants
     */
    public function index(Request $request): JsonResponse
    {
        $query = Grant::with(['submitter', 'policyVersion', 'workflow']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('award_year')) {
            $query->where('award_year', $request->award_year);
        }

        if ($request->has('grant_type')) {
            $query->where('grant_type', $request->grant_type);
        }

        if ($request->has('user_id')) {
            $query->where('submitted_by', $request->user_id);
        }

        $grants = $query->latest()->paginate($request->get('per_page', 15));

        return $this->successResponse($grants, 'Grants retrieved successfully');
    }

    /**
     * Store a newly created grant
     */
    public function store(StoreGrantRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['submitted_by'] = auth()->id();
        $data['slug'] = \Str::slug($data['title']);

        $grant = Grant::create($data);

        // Calculate units if amount_omr is provided
        if (isset($data['amount_omr'])) {
            $grant->calculateUnits();
        }

        // Calculate points
        $this->scoringService->calculateGrantPoints($grant);

        // Create workflow if not exists
        if (!$grant->workflow) {
            $this->workflowService->createWorkflow('grant', $grant->id, auth()->user());
        }

        return $this->successResponse($grant->load(['submitter', 'workflow']), 'Grant created successfully', 201);
    }

    /**
     * Display the specified grant
     */
    public function show(Grant $grant): JsonResponse
    {
        $grant->load(['submitter', 'policyVersion', 'workflow', 'evidenceFiles']);
        return $this->successResponse($grant, 'Grant retrieved successfully');
    }

    /**
     * Update the specified grant
     */
    public function update(UpdateGrantRequest $request, Grant $grant): JsonResponse
    {
        // Check if grant can be edited
        if ($grant->status === 'approved' && $grant->points_locked) {
            return $this->errorResponse('Cannot edit approved grant with locked points', 403);
        }

        $data = $request->validated();
        
        if (isset($data['title']) && $data['title'] !== $grant->title) {
            $data['slug'] = \Str::slug($data['title']);
        }

        $grant->update($data);

        // Recalculate units if amount_omr changed
        if (isset($data['amount_omr'])) {
            $grant->calculateUnits();
        }

        // Recalculate points if relevant fields changed
        if (isset($data['grant_type']) || isset($data['role']) || isset($data['amount_omr'])) {
            $this->scoringService->calculateGrantPoints($grant);
        }

        return $this->successResponse($grant->fresh()->load(['submitter', 'workflow']), 'Grant updated successfully');
    }

    /**
     * Remove the specified grant
     */
    public function destroy(Grant $grant): JsonResponse
    {
        if ($grant->status === 'approved') {
            return $this->errorResponse('Cannot delete approved grant', 403);
        }

        $grant->delete();

        return $this->successResponse(null, 'Grant deleted successfully');
    }

    /**
     * Submit grant for approval
     */
    public function submit(Grant $grant): JsonResponse
    {
        if ($grant->submitted_by !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($grant->status !== 'draft') {
            return $this->errorResponse('Grant already submitted', 400);
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'grant')
            ->where('submission_id', $grant->id)
            ->first();
            
        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('grant', $grant->id, auth()->user());
        }
        
        $workflow = $this->workflowService->submitWorkflow($workflow);

        $grant->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $this->successResponse($grant->load('workflow'), 'Grant submitted for approval');
    }
}

