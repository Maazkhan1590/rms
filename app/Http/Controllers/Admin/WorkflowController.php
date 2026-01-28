<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalHistory;
use App\Services\WorkflowService;
use App\Services\ScoringService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkflowController extends Controller
{
    protected WorkflowService $workflowService;
    protected ScoringService $scoringService;

    public function __construct(WorkflowService $workflowService, ScoringService $scoringService)
    {
        $this->workflowService = $workflowService;
        $this->scoringService = $scoringService;
    }

    /**
     * Display a listing of all workflows
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('workflow_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ApprovalWorkflow::with(['submitter', 'assignee']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by submission type
        if ($request->has('type') && $request->type) {
            $query->where('submission_type', $request->type);
        }

        // Filter by assigned user
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by submitter
        if ($request->has('submitted_by') && $request->submitted_by) {
            $query->where('submitted_by', $request->submitted_by);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('college', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $workflows = $query->latest('created_at')->paginate(20);

        // Get filter options
        $statuses = ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned'];
        $types = ['publication', 'grant', 'rtn', 'bonus'];
        // Get unique user IDs from workflows
        $submittedBy = ApprovalWorkflow::distinct()->pluck('submitted_by')->filter();
        $assignedTo = ApprovalWorkflow::distinct()->pluck('assigned_to')->filter();
        $userIds = $submittedBy->merge($assignedTo)->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id');

        // Count pending workflows
        $pendingCount = ApprovalWorkflow::pending()->count();

        return view('admin.workflows.index', compact('workflows', 'statuses', 'types', 'users', 'pendingCount'));
    }

    /**
     * Display pending workflows
     */
    public function pending(Request $request)
    {
        abort_if(Gate::denies('workflow_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ApprovalWorkflow::with(['submitter', 'assignee'])
            ->pending();

        // Filter by assigned to current user
        if ($request->has('my_workflows') && $request->my_workflows) {
            $query->where('assigned_to', auth()->id());
        }

        // Filter by submission type
        if ($request->has('type') && $request->type) {
            $query->where('submission_type', $request->type);
        }

        $workflows = $query->latest('created_at')->paginate(20);

        $pendingCount = ApprovalWorkflow::pending()->count();
        $types = ['publication', 'grant', 'rtn', 'bonus'];

        return view('admin.workflows.pending', compact('workflows', 'types', 'pendingCount'));
    }

    /**
     * Display the specified workflow
     */
    public function show(ApprovalWorkflow $workflow)
    {
        abort_if(Gate::denies('workflow_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $workflow->load(['submitter', 'assignee', 'history.performer']);

        // Get the submission
        $submission = $workflow->submission;
        
        // Get eligible users for reassignment based on current workflow step
        $eligibleUsers = [];
        if ($workflow->current_step == 2) {
            // Coordinator step - get coordinators and admins
            $eligibleUsers = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('title', ['Coordinator', 'Admin']);
            })->pluck('name', 'id');
        } elseif ($workflow->current_step == 3) {
            // Dean step - get deans and admins
            $eligibleUsers = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('title', ['Dean', 'Admin']);
            })->pluck('name', 'id');
        } else {
            // Default - get all coordinators, deans, and admins
            $eligibleUsers = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('title', ['Coordinator', 'Dean', 'Admin']);
            })->pluck('name', 'id');
        }

        return view('admin.workflows.show', compact('workflow', 'submission', 'eligibleUsers'));
    }

    /**
     * Approve workflow
     */
    public function approve(Request $request, ApprovalWorkflow $workflow)
    {
        abort_if(Gate::denies('workflow_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Approve workflow
            $workflow = $this->workflowService->approveWorkflow(
                $workflow,
                auth()->user(),
                $request->comments ?? 'Approved by admin'
            );

            // If workflow is fully approved, calculate points
            if ($workflow->status === 'approved') {
                $submission = $workflow->submission;
                
                if ($submission) {
                    // Calculate points based on submission type
                    switch ($workflow->submission_type) {
                        case 'publication':
                            if ($submission instanceof \App\Models\Publication) {
                                $this->scoringService->calculatePublicationPoints($submission);
                                if ($submission->primary_author_id) {
                                    $this->scoringService->recalculateUserTotalPoints(
                                        $submission->primary_author_id,
                                        $submission->publication_year ?? $submission->year
                                    );
                                }
                            }
                            break;
                        case 'grant':
                            if ($submission instanceof \App\Models\Grant) {
                                $this->scoringService->calculateGrantPoints($submission);
                                if ($submission->submitted_by) {
                                    $this->scoringService->recalculateUserTotalPoints(
                                        $submission->submitted_by,
                                        $submission->award_year
                                    );
                                }
                            }
                            break;
                        case 'rtn':
                            if ($submission instanceof \App\Models\RtnSubmission) {
                                $this->scoringService->calculateRtnPoints($submission);
                                if ($submission->user_id) {
                                    $this->scoringService->recalculateUserTotalPoints(
                                        $submission->user_id,
                                        $submission->year
                                    );
                                }
                            }
                            break;
                        case 'bonus':
                            if ($submission instanceof \App\Models\BonusRecognition) {
                                $this->scoringService->calculateBonusPoints($submission);
                                if ($submission->user_id) {
                                    $this->scoringService->recalculateUserTotalPoints(
                                        $submission->user_id,
                                        $submission->year
                                    );
                                }
                            }
                            break;
                    }
                }
            }

            \DB::commit();

            $message = $workflow->status === 'approved'
                ? 'Workflow approved successfully. Points have been calculated and allocated.'
                : 'Workflow approved at current step.';

            return redirect()->route('admin.workflows.show', $workflow->id)
                ->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving workflow: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error approving workflow: ' . $e->getMessage());
        }
    }

    /**
     * Reject workflow
     */
    public function reject(Request $request, ApprovalWorkflow $workflow)
    {
        abort_if(Gate::denies('workflow_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->workflowService->rejectWorkflow(
                $workflow,
                auth()->user(),
                $request->reason
            );

            // Update submission status
            $submission = $workflow->submission;
            if ($submission) {
                $submission->status = 'rejected';
                $submission->save();
            }

            return redirect()->route('admin.workflows.show', $workflow->id)
                ->with('success', 'Workflow rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting workflow: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error rejecting workflow: ' . $e->getMessage());
        }
    }

    /**
     * Return workflow for revision
     */
    public function return(Request $request, ApprovalWorkflow $workflow)
    {
        abort_if(Gate::denies('workflow_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'required|string|max:500',
        ]);

        try {
            $this->workflowService->returnWorkflow(
                $workflow,
                auth()->user(),
                $request->comments
            );

            // Update submission status
            $submission = $workflow->submission;
            if ($submission) {
                $submission->status = 'draft';
                $submission->save();
            }

            return redirect()->route('admin.workflows.show', $workflow->id)
                ->with('success', 'Workflow returned for revision.');
        } catch (\Exception $e) {
            \Log::error('Error returning workflow: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error returning workflow: ' . $e->getMessage());
        }
    }

    /**
     * Manually reassign workflow to a different user (Admin only)
     * 
     * Allows admins to reassign workflows when approvers are unavailable
     */
    public function reassign(Request $request, ApprovalWorkflow $workflow)
    {
        abort_if(Gate::denies('workflow_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $newAssignee = \App\Models\User::findOrFail($request->assigned_to);
            
            // Verify user has appropriate role for the current workflow step
            $currentStep = $workflow->current_step;
            $isValidAssignee = false;
            
            if ($currentStep == 2 && ($newAssignee->isResearchCoordinator() || $newAssignee->isAdmin())) {
                $isValidAssignee = true;
            } elseif ($currentStep == 3 && ($newAssignee->isDean() || $newAssignee->isAdmin())) {
                $isValidAssignee = true;
            } elseif ($newAssignee->isAdmin()) {
                $isValidAssignee = true; // Admin can always be assigned
            }
            
            if (!$isValidAssignee) {
                return redirect()->back()
                    ->with('error', 'Selected user does not have the appropriate role for this workflow step.');
            }

            $this->workflowService->reassignWorkflow(
                $workflow,
                $newAssignee,
                auth()->user(),
                $request->reason ?? 'Manually reassigned by admin'
            );

            return redirect()->route('admin.workflows.show', $workflow->id)
                ->with('success', 'Workflow reassigned successfully.');
        } catch (\Exception $e) {
            \Log::error('Error reassigning workflow: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error reassigning workflow: ' . $e->getMessage());
        }
    }
}
