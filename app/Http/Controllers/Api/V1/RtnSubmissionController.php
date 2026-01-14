<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponser;
use App\Http\Requests\StoreRtnSubmissionRequest;
use App\Http\Requests\UpdateRtnSubmissionRequest;
use App\Models\RtnSubmission;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RtnSubmissionController extends Controller
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
     * Display a listing of RTN submissions
     */
    public function index(Request $request): JsonResponse
    {
        $query = RtnSubmission::with(['user', 'workflow']);

        // Filter by user if not admin
        if (!auth()->user()->isAdmin) {
            $query->where('user_id', auth()->id());
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('rtn_type')) {
            $query->where('rtn_type', $request->rtn_type);
        }

        $submissions = $query->latest()->paginate($request->get('per_page', 15));

        return $this->successResponse($submissions, 'RTN submissions retrieved successfully');
    }

    /**
     * Store a newly created RTN submission
     */
    public function store(StoreRtnSubmissionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['year'] = $data['year'] ?? now()->year;

        $submission = RtnSubmission::create($data);

        // Calculate points
        $this->scoringService->calculateRtnPoints($submission);

        // Create workflow if not exists
        if (!$submission->workflow) {
            $this->workflowService->createWorkflow('rtn', $submission->id, auth()->user());
        }

        return $this->successResponse($submission->load(['user', 'workflow']), 'RTN submission created successfully', 201);
    }

    /**
     * Display the specified RTN submission
     */
    public function show(RtnSubmission $rtnSubmission): JsonResponse
    {
        // Check authorization
        if (!$rtnSubmission->user_id === auth()->id() && !auth()->user()->isAdmin) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $rtnSubmission->load(['user', 'workflow']);
        return $this->successResponse($rtnSubmission, 'RTN submission retrieved successfully');
    }

    /**
     * Update the specified RTN submission
     */
    public function update(UpdateRtnSubmissionRequest $request, RtnSubmission $rtnSubmission): JsonResponse
    {
        // Check authorization
        if ($rtnSubmission->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($rtnSubmission->status === 'approved') {
            return $this->errorResponse('Cannot edit approved RTN submission', 403);
        }

        $rtnSubmission->update($request->validated());

        return $this->successResponse($rtnSubmission->fresh()->load(['user', 'workflow']), 'RTN submission updated successfully');
    }

    /**
     * Remove the specified RTN submission
     */
    public function destroy(RtnSubmission $rtnSubmission): JsonResponse
    {
        if ($rtnSubmission->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($rtnSubmission->status === 'approved') {
            return $this->errorResponse('Cannot delete approved RTN submission', 403);
        }

        $rtnSubmission->delete();

        return $this->successResponse(null, 'RTN submission deleted successfully');
    }

    /**
     * Submit RTN for approval
     */
    public function submit(RtnSubmission $rtnSubmission): JsonResponse
    {
        if ($rtnSubmission->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($rtnSubmission->status !== 'draft') {
            return $this->errorResponse('RTN submission already submitted', 400);
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'rtn')
            ->where('submission_id', $rtnSubmission->id)
            ->first();
            
        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('rtn', $rtnSubmission->id, auth()->user());
        }
        
        $workflow = $this->workflowService->submitWorkflow($workflow);

        $rtnSubmission->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $this->successResponse($rtnSubmission->load('workflow'), 'RTN submission submitted for approval');
    }
}

