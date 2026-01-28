<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RtnSubmission;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RtnSubmissionController extends Controller
{
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
    public function index(Request $request)
    {
        abort_if(Gate::denies('rtn_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = RtnSubmission::with(['user']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by RTN type
        if ($request->has('type') && $request->type) {
            $query->where('rtn_type', $request->type);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $submissions = $query->latest('created_at')->paginate(20);

        // Get filter options
        $statuses = ['pending', 'submitted', 'approved', 'rejected', 'draft'];
        $years = RtnSubmission::distinct()->pluck('year')->filter()->sortDesc()->values();
        $types = RtnSubmission::distinct()->pluck('rtn_type')->filter()->sort()->values();
        $users = User::whereHas('rtnSubmissions')->pluck('name', 'id');

        return view('admin.rtn-submissions.index', compact('submissions', 'statuses', 'years', 'types', 'users'));
    }

    /**
     * Show the form for creating a new RTN submission
     * Admin cannot create - only users can submit
     */
    public function create()
    {
        return redirect()->route('admin.rtn-submissions.index')
            ->with('info', 'RTN submissions can only be created by users through their submissions.');
    }

    /**
     * Store a newly created RTN submission
     * Admin cannot create - only users can submit
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.rtn-submissions.index')
            ->with('info', 'RTN submissions can only be created by users through their submissions.');
    }

    /**
     * Display the specified RTN submission
     */
    public function show(RtnSubmission $rtnSubmission)
    {
        abort_if(Gate::denies('rtn_submission_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rtnSubmission->load(['user', 'workflow']);

        return view('admin.rtn-submissions.show', compact('rtnSubmission'));
    }

    /**
     * Show the form for editing the specified RTN submission
     * Admin cannot edit - only approve/reject
     */
    public function edit(RtnSubmission $rtnSubmission)
    {
        return redirect()->route('admin.rtn-submissions.show', $rtnSubmission)
            ->with('info', 'Admin users can only approve or reject RTN submissions. Editing is not allowed.');
    }

    /**
     * Update the specified RTN submission
     * Admin cannot edit - only approve/reject
     */
    public function update(Request $request, RtnSubmission $rtnSubmission)
    {
        return redirect()->route('admin.rtn-submissions.show', $rtnSubmission)
            ->with('info', 'Admin users can only approve or reject RTN submissions. Editing is not allowed.');
    }

    /**
     * Remove the specified RTN submission
     */
    public function destroy(RtnSubmission $rtnSubmission)
    {
        abort_if(Gate::denies('rtn_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rtnSubmission->delete();

        return redirect()->route('admin.rtn-submissions.index')
            ->with('success', 'RTN submission deleted successfully.');
    }

    /**
     * Approve RTN submission
     * Integrates with workflow and scoring systems
     */
    public function approve(RtnSubmission $rtnSubmission)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            // Find workflow - MUST exist for approval
            $workflow = ApprovalWorkflow::where('submission_type', 'rtn')
                ->where('submission_id', $rtnSubmission->id)
                ->first();

            // STRICT WORKFLOW: Workflow must exist - no admin bypass
            if (!$workflow) {
                \DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No workflow found for this RTN submission. The submission must be submitted through the proper workflow process.');
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
                    ->with('error', 'You are not authorized to approve this RTN submission. Only the assigned Coordinator or Dean can approve at the current workflow step.');
            }

            if ($workflow) {
                
                // Use workflow service to approve
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                
                // Refresh workflow to get latest status
                $workflow->refresh();
                
                // Refresh submission to get any updates from finalizeApproval
                $rtnSubmission->refresh();
                
                // If workflow is fully approved, calculate points
                if ($workflow->status === 'approved') {
                    // Calculate and assign points (RTN always gets 5 points)
                    $points = $this->scoringService->calculateRtnPoints($rtnSubmission->fresh());
                    
                    // Recalculate user's total points
                    if ($rtnSubmission->user_id) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $rtnSubmission->user_id,
                            $rtnSubmission->year
                        );
                    }
                } else {
                    // Workflow still in progress - update status based on workflow status
                    $rtnSubmission->update([
                        'status' => $workflow->status == 'pending_coordinator' ? 'pending_coordinator' : 
                                   ($workflow->status == 'pending_dean' ? 'pending_dean' : 'submitted'),
                    ]);
                }
            }

            \DB::commit();

            $message = $workflow && $workflow->status !== 'approved' 
                ? 'RTN submission approved at current workflow step.'
                : 'RTN submission approved successfully. Points allocated: ' . ($rtnSubmission->points ?? 5);

            return redirect()->route('admin.rtn-submissions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving RTN submission: ' . $e->getMessage());
            
            return redirect()->route('admin.rtn-submissions.index')
                ->with('error', 'Error approving RTN submission: ' . $e->getMessage());
        }
    }

    /**
     * Reject RTN submission
     * Integrates with workflow system
     */
    public function reject(Request $request, RtnSubmission $rtnSubmission)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Find workflow if exists
            $workflow = ApprovalWorkflow::where('submission_type', 'rtn')
                ->where('submission_id', $rtnSubmission->id)
                ->first();

            if ($workflow) {
                // Use workflow service to reject
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->reason ?? 'Rejected by admin');
            }

            // Update submission status
            $rtnSubmission->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            return redirect()->route('admin.rtn-submissions.index')
                ->with('success', 'RTN submission rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error rejecting RTN submission: ' . $e->getMessage());
            
            return redirect()->route('admin.rtn-submissions.index')
                ->with('error', 'Error rejecting RTN submission: ' . $e->getMessage());
        }
    }
}
