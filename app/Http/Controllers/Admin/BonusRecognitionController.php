<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BonusRecognition;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BonusRecognitionController extends Controller
{
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
    public function index(Request $request)
    {
        abort_if(Gate::denies('bonus_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = BonusRecognition::with(['user']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by recognition type
        if ($request->has('type') && $request->type) {
            $query->where('recognition_type', $request->type);
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
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('journal_conference_name', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $recognitions = $query->latest('created_at')->paginate(20);

        // Get filter options
        $statuses = ['pending', 'submitted', 'approved', 'rejected', 'draft'];
        $years = BonusRecognition::distinct()->pluck('year')->filter()->sortDesc()->values();
        $types = BonusRecognition::distinct()->pluck('recognition_type')->filter()->sort()->values();
        $users = User::whereHas('bonusRecognitions')->pluck('name', 'id');

        return view('admin.bonus-recognitions.index', compact('recognitions', 'statuses', 'years', 'types', 'users'));
    }

    /**
     * Show the form for creating a new bonus recognition
     * Admin cannot create - only users can submit
     */
    public function create()
    {
        return redirect()->route('admin.bonus-recognitions.index')
            ->with('info', 'Bonus recognitions can only be created by users through their submissions.');
    }

    /**
     * Store a newly created bonus recognition
     * Admin cannot create - only users can submit
     */
    public function store(Request $request)
    {
        return redirect()->route('admin.bonus-recognitions.index')
            ->with('info', 'Bonus recognitions can only be created by users through their submissions.');
    }

    /**
     * Display the specified bonus recognition
     */
    public function show(BonusRecognition $bonusRecognition)
    {
        abort_if(Gate::denies('bonus_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bonusRecognition->load(['user', 'workflow']);

        return view('admin.bonus-recognitions.show', compact('bonusRecognition'));
    }

    /**
     * Show the form for editing the specified bonus recognition
     * Admin cannot edit - only approve/reject
     */
    public function edit(BonusRecognition $bonusRecognition)
    {
        return redirect()->route('admin.bonus-recognitions.show', $bonusRecognition)
            ->with('info', 'Admin users can only approve or reject bonus recognitions. Editing is not allowed.');
    }

    /**
     * Update the specified bonus recognition
     * Admin cannot edit - only approve/reject
     */
    public function update(Request $request, BonusRecognition $bonusRecognition)
    {
        return redirect()->route('admin.bonus-recognitions.show', $bonusRecognition)
            ->with('info', 'Admin users can only approve or reject bonus recognitions. Editing is not allowed.');
    }

    /**
     * Remove the specified bonus recognition
     */
    public function destroy(BonusRecognition $bonusRecognition)
    {
        abort_if(Gate::denies('bonus_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bonusRecognition->delete();

        return redirect()->route('admin.bonus-recognitions.index')
            ->with('success', 'Bonus recognition deleted successfully.');
    }

    /**
     * Approve bonus recognition
     * Integrates with workflow and scoring systems
     */
    public function approve(BonusRecognition $bonusRecognition)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            // Find workflow - MUST exist for approval
            $workflow = ApprovalWorkflow::where('submission_type', 'bonus')
                ->where('submission_id', $bonusRecognition->id)
                ->first();

            // STRICT WORKFLOW: Workflow must exist - no admin bypass
            if (!$workflow) {
                \DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No workflow found for this bonus recognition. The recognition must be submitted through the proper workflow process.');
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
                    ->with('error', 'You are not authorized to approve this bonus recognition. Only the assigned Coordinator or Dean can approve at the current workflow step.');
            }

            if ($workflow) {
                
                // Use workflow service to approve
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                
                // Refresh workflow to get latest status
                $workflow->refresh();
                
                // Refresh recognition to get any updates from finalizeApproval
                $bonusRecognition->refresh();
                
                // If workflow is fully approved, calculate points
                if ($workflow->status === 'approved') {
                    // Calculate and assign points
                    $points = $this->scoringService->calculateBonusPoints($bonusRecognition->fresh());
                    
                    // Recalculate user's total points
                    if ($bonusRecognition->user_id) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $bonusRecognition->user_id,
                            $bonusRecognition->year
                        );
                    }
                } else {
                    // Workflow still in progress - update status based on workflow status
                    $bonusRecognition->update([
                        'status' => $workflow->status == 'pending_coordinator' ? 'pending_coordinator' : 
                                   ($workflow->status == 'pending_dean' ? 'pending_dean' : 'submitted'),
                    ]);
                }
            }
                
                // Recalculate user's total points
                if ($bonusRecognition->user_id) {
                    $this->scoringService->recalculateUserTotalPoints(
                        $bonusRecognition->user_id,
                        $bonusRecognition->year
                    );
                }
            }

            \DB::commit();

            $message = $workflow && $workflow->status !== 'approved' 
                ? 'Bonus recognition approved at current workflow step.'
                : 'Bonus recognition approved successfully. Points allocated: ' . ($bonusRecognition->points ?? 0);

            return redirect()->route('admin.bonus-recognitions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving bonus recognition: ' . $e->getMessage());
            
            return redirect()->route('admin.bonus-recognitions.index')
                ->with('error', 'Error approving bonus recognition: ' . $e->getMessage());
        }
    }

    /**
     * Reject bonus recognition
     * Integrates with workflow system
     */
    public function reject(Request $request, BonusRecognition $bonusRecognition)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Find workflow if exists
            $workflow = ApprovalWorkflow::where('submission_type', 'bonus')
                ->where('submission_id', $bonusRecognition->id)
                ->first();

            if ($workflow) {
                // Use workflow service to reject
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->reason ?? 'Rejected by admin');
            }

            // Update recognition status
            $bonusRecognition->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            return redirect()->route('admin.bonus-recognitions.index')
                ->with('success', 'Bonus recognition rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error rejecting bonus recognition: ' . $e->getMessage());
            
            return redirect()->route('admin.bonus-recognitions.index')
                ->with('error', 'Error rejecting bonus recognition: ' . $e->getMessage());
        }
    }
}
