<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponser;
use App\Models\ApprovalWorkflow;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkflowController extends Controller
{
    use ApiResponser;

    protected WorkflowService $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Get pending workflows for current user
     */
    public function pending(Request $request): JsonResponse
    {
        $query = ApprovalWorkflow::with(['submitter', 'assignee', 'submission'])
            ->pending();

        // Filter by assigned user
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        } else {
            // Show workflows assigned to current user
            $query->where('assigned_to', auth()->id());
        }

        $workflows = $query->latest()->paginate($request->get('per_page', 15));

        return $this->successResponse($workflows, 'Pending workflows retrieved successfully');
    }

    /**
     * Approve a workflow
     */
    public function approve(Request $request, ApprovalWorkflow $workflow): JsonResponse
    {
        if ($workflow->assigned_to !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $comments = $request->input('comments');

        $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), $comments);

        return $this->successResponse($workflow->load(['submitter', 'assignee', 'submission', 'history']), 'Workflow approved successfully');
    }

    /**
     * Reject a workflow
     */
    public function reject(Request $request, ApprovalWorkflow $workflow): JsonResponse
    {
        if ($workflow->assigned_to !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $request->validate([
            'comments' => 'required|string|min:10',
        ]);

        $workflow = $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments);

        return $this->successResponse($workflow->load(['submitter', 'assignee', 'submission', 'history']), 'Workflow rejected');
    }

    /**
     * Return a workflow for revision
     */
    public function return(Request $request, ApprovalWorkflow $workflow): JsonResponse
    {
        if ($workflow->assigned_to !== auth()->id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $request->validate([
            'comments' => 'required|string|min:10',
        ]);

        $workflow = $this->workflowService->returnWorkflow($workflow, auth()->user(), $request->comments);

        return $this->successResponse($workflow->load(['submitter', 'assignee', 'submission', 'history']), 'Workflow returned for revision');
    }

    /**
     * Get workflow history
     */
    public function history(ApprovalWorkflow $workflow): JsonResponse
    {
        $history = $workflow->history()->with('performer')->latest()->get();

        return $this->successResponse($history, 'Workflow history retrieved successfully');
    }
}

