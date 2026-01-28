<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grant;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GrantController extends Controller
{
    protected ScoringService $scoringService;
    protected WorkflowService $workflowService;
    protected LoggingService $loggingService;

    public function __construct(ScoringService $scoringService, WorkflowService $workflowService, LoggingService $loggingService)
    {
        $this->scoringService = $scoringService;
        $this->workflowService = $workflowService;
        $this->loggingService = $loggingService;
    }

    /**
     * Display a listing of grants
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('grant_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = Grant::with(['submitter', 'approver']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('award_year', $request->year);
        }

        // Filter by grant type
        if ($request->has('type') && $request->type) {
            $query->where('grant_type', $request->type);
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('submitted_by', $request->user_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('sponsor', 'like', "%{$search}%")
                  ->orWhere('sponsor_name', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $grants = $query->latest('created_at')->paginate(20);

        // Get filter options
        $statuses = ['pending', 'submitted', 'approved', 'rejected', 'draft'];
        $years = Grant::distinct()->pluck('award_year')->filter()->sortDesc()->values();
        $types = Grant::distinct()->pluck('grant_type')->filter()->sort()->values();
        $roles = Grant::distinct()->pluck('role')->filter()->sort()->values();
        $users = User::whereHas('grants')->pluck('name', 'id');

        return view('admin.grants.index', compact('grants', 'statuses', 'years', 'types', 'roles', 'users'));
    }

    /**
     * Show the form for creating a new grant
     * Admin cannot create - only users can submit
     */
    public function create()
    {
        return redirect()->route('admin.grants.index')
            ->with('info', 'Grants can only be created by users through their submissions.');
    }

    /**
     * Store a newly created grant
     * Admin cannot create - only users can submit
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.grants.index')
            ->with('info', 'Grants can only be created by users through their submissions.');
    }

    /**
     * Display the specified grant
     */
    public function show(Grant $grant)
    {
        abort_if(Gate::denies('grant_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $grant->load(['submitter', 'approver', 'workflow', 'evidenceFiles']);

        return view('admin.grants.show', compact('grant'));
    }

    /**
     * Show the form for editing the specified grant
     * Admin cannot edit - only approve/reject
     */
    public function edit(Grant $grant)
    {
        return redirect()->route('admin.grants.show', $grant)
            ->with('info', 'Admin users can only approve or reject grants. Editing is not allowed.');
    }

    /**
     * Update the specified grant
     * Admin cannot edit - only approve/reject
     */
    public function update(Request $request, Grant $grant)
    {
        return redirect()->route('admin.grants.show', $grant)
            ->with('info', 'Admin users can only approve or reject grants. Editing is not allowed.');
    }

    /**
     * Remove the specified grant
     */
    public function destroy(Grant $grant)
    {
        abort_if(Gate::denies('grant_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $grant->delete();

        return redirect()->route('admin.grants.index')
            ->with('success', 'Grant deleted successfully.');
    }

    /**
     * Approve grant
     * Integrates with workflow and scoring systems
     */
    public function approve(Grant $grant)
    {
        abort_if(Gate::denies('grant_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            // Find workflow - create default if doesn't exist
            $workflow = ApprovalWorkflow::where('submission_type', 'grant')
                ->where('submission_id', $grant->id)
                ->first();

            // If no workflow exists, create a default one and submit it
            // This handles cases where grant was submitted before workflow was created
            if (!$workflow) {
                // Create workflow in draft status
                $workflow = $this->workflowService->createWorkflow('grant', $grant->id, $grant->submitter ?? auth()->user());
                
                // If grant is already submitted (not draft), submit the workflow
                if (in_array($grant->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
                    $workflow = $this->workflowService->submitWorkflow($workflow);
                }
            }

            // Check if user can approve this workflow step (STRICT - NO ADMIN BYPASS)
            $user = auth()->user();
            $canApprove = false;
            
            if ($workflow->assigned_to == $user->id) {
                $canApprove = true;
            } elseif ($workflow->status == 'pending_coordinator' && $user->isResearchCoordinator()) {
                $canApprove = true;
            } elseif ($workflow->status == 'pending_dean' && $user->isDean()) {
                $canApprove = true;
            }
            
            if (!$canApprove) {
                \DB::rollBack();
                return redirect()->back()
                    ->with('error', 'You are not authorized to approve this grant. Only the assigned Coordinator or Dean can approve at the current workflow step.');
            }

            if ($workflow) {
                
                // Use workflow service to approve
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                
                // Refresh workflow to get latest status
                $workflow->refresh();
                
                // Refresh grant to get any updates from finalizeApproval
                $grant->refresh();
                
                // If workflow is fully approved, calculate points
                if ($workflow->status === 'approved') {
                    // Calculate and assign points
                    $points = $this->scoringService->calculateGrantPoints($grant->fresh());
                    
                    // Recalculate user's total points
                    if ($grant->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $grant->submitted_by,
                            $grant->award_year ?? $grant->submission_year
                        );
                    }
                } else {
                    // Workflow still in progress - update status based on workflow status
                    $grant->update([
                        'status' => $workflow->status == 'pending_coordinator' ? 'pending_coordinator' : 
                                   ($workflow->status == 'pending_dean' ? 'pending_dean' : 'submitted'),
                    ]);
                }
            }

            \DB::commit();

            $message = $workflow && $workflow->status !== 'approved' 
                ? 'Grant approved at current workflow step.'
                : 'Grant approved successfully. Points allocated: ' . ($grant->points_allocated ?? 0);

            return redirect()->route('admin.grants.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving grant: ' . $e->getMessage());
            
            return redirect()->route('admin.grants.index')
                ->with('error', 'Error approving grant: ' . $e->getMessage());
        }
    }

    /**
     * Reject grant
     * Integrates with workflow system
     */
    public function reject(Request $request, Grant $grant)
    {
        abort_if(Gate::denies('grant_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Find workflow if exists
            $workflow = ApprovalWorkflow::where('submission_type', 'grant')
                ->where('submission_id', $grant->id)
                ->first();

            if ($workflow) {
                // Use workflow service to reject
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->reason ?? 'Rejected by admin');
            }

            // Update grant status
            $oldStatus = $grant->status;
            $grant->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            // Log audit
            $oldValues = ['status' => $oldStatus];
            $newValues = ['status' => $grant->status];
            $this->loggingService->logAudit('grant.rejected', $grant, $oldValues, $newValues);

            // Log activity
            $this->loggingService->logActivity(
                'grant_rejected',
                "Rejected grant: {$grant->title}",
                Grant::class,
                $grant->id
            );

            return redirect()->route('admin.grants.index')
                ->with('success', 'Grant rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error rejecting grant: ' . $e->getMessage());
            
            return redirect()->route('admin.grants.index')
                ->with('error', 'Error rejecting grant: ' . $e->getMessage());
        }
    }
}
