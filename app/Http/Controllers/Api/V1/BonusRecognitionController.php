<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponser;
use App\Http\Requests\StoreBonusRecognitionRequest;
use App\Http\Requests\UpdateBonusRecognitionRequest;
use App\Models\BonusRecognition;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BonusRecognitionController extends Controller
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
     * Display a listing of bonus recognitions
     */
    public function index(Request $request): JsonResponse
    {
        $query = BonusRecognition::with(['user', 'workflow']);

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

        if ($request->has('recognition_type')) {
            $query->where('recognition_type', $request->recognition_type);
        }

        $recognitions = $query->latest()->paginate($request->get('per_page', 15));

        return $this->successResponse($recognitions, 'Bonus recognitions retrieved successfully');
    }

    /**
     * Store a newly created bonus recognition
     */
    public function store(StoreBonusRecognitionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['year'] = $data['year'] ?? now()->year;

        $recognition = BonusRecognition::create($data);

        // Calculate points
        $this->scoringService->calculateBonusPoints($recognition);

        // Create workflow if not exists
        if (!$recognition->workflow) {
            $this->workflowService->createWorkflow('bonus', $recognition->id, auth()->user());
        }

        return $this->successResponse($recognition->load(['user', 'workflow']), 'Bonus recognition created successfully', 201);
    }

    /**
     * Display the specified bonus recognition
     */
    public function show(BonusRecognition $bonusRecognition): JsonResponse
    {
        // Check authorization
        if ($bonusRecognition->user_id !== auth()->id() && !auth()->user()->isAdmin) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $bonusRecognition->load(['user', 'workflow']);
        return $this->successResponse($bonusRecognition, 'Bonus recognition retrieved successfully');
    }

    /**
     * Update the specified bonus recognition
     */
    public function update(UpdateBonusRecognitionRequest $request, BonusRecognition $bonusRecognition): JsonResponse
    {
        // Check authorization
        if ($bonusRecognition->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($bonusRecognition->status === 'approved') {
            return $this->errorResponse('Cannot edit approved bonus recognition', 403);
        }

        $bonusRecognition->update($request->validated());

        // Recalculate points if recognition_type changed
        if ($request->has('recognition_type')) {
            $this->scoringService->calculateBonusPoints($bonusRecognition);
        }

        return $this->successResponse($bonusRecognition->fresh()->load(['user', 'workflow']), 'Bonus recognition updated successfully');
    }

    /**
     * Remove the specified bonus recognition
     */
    public function destroy(BonusRecognition $bonusRecognition): JsonResponse
    {
        if ($bonusRecognition->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($bonusRecognition->status === 'approved') {
            return $this->errorResponse('Cannot delete approved bonus recognition', 403);
        }

        $bonusRecognition->delete();

        return $this->successResponse(null, 'Bonus recognition deleted successfully');
    }

    /**
     * Submit bonus recognition for approval
     */
    public function submit(BonusRecognition $bonusRecognition): JsonResponse
    {
        if ($bonusRecognition->user_id !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($bonusRecognition->status !== 'draft') {
            return $this->errorResponse('Bonus recognition already submitted', 400);
        }

        $workflow = ApprovalWorkflow::where('submission_type', 'bonus')
            ->where('submission_id', $bonusRecognition->id)
            ->first();
            
        if (!$workflow) {
            $workflow = $this->workflowService->createWorkflow('bonus', $bonusRecognition->id, auth()->user());
        }
        
        $workflow = $this->workflowService->submitWorkflow($workflow);

        $bonusRecognition->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $this->successResponse($bonusRecognition->load('workflow'), 'Bonus recognition submitted for approval');
    }
}

