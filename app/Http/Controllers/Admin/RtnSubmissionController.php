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

        // If AJAX request for DataTables
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        return view('admin.rtn-submissions.index');
    }

    /**
     * Get DataTables data
     */
    private function getDataTableData(Request $request)
    {
        $query = RtnSubmission::with(['user', 'workflow']);

        // If current user is Faculty (non-admin), always show only their own RTN submissions
        $user = auth()->user();
        if ($user && $user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean()) {
            $query->where('user_id', $user->id);
        }

        // Exclude drafts by default
        $query->where('status', '!=', 'draft');

        // Global search
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Get total count before pagination
        $totalRecords = $query->count();

        // Ordering
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'title', 'rtn_type', 'user_id', 'evidence_link', 'total_rtn', 'year', 'status', 'workflow.status', 'points_allocated', 'submitted_at'];
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        if ($orderBy === 'user_id') {
            $query->leftJoin('users', 'rtn_submissions.user_id', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('rtn_submissions.*');
        } elseif ($orderBy === 'workflow.status') {
            $query->leftJoin('approval_workflows', function($join) {
                $join->on('rtn_submissions.id', '=', 'approval_workflows.submission_id')
                     ->where('approval_workflows.submission_type', '=', 'rtn');
            })
            ->orderBy('approval_workflows.status', $orderDir)
            ->select('rtn_submissions.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);
        $submissions = $query->skip($start)->take($length)->get();

        // Reload relationships if they were lost due to joins
        $submissions->load(['user', 'workflow']);

        // Format data for DataTables
        $data = $submissions->map(function($submission) use ($user) {
            $workflow = $submission->workflow ?? \App\Models\ApprovalWorkflow::where('submission_type', 'rtn')
                ->where('submission_id', $submission->id)
                ->first();
            
            $workflowBadge = '<span class="badge badge-secondary">No Workflow</span>';
            
            if ($workflow) {
                if ($workflow->status == 'pending_coordinator') {
                    $workflowBadge = '<span class="badge badge-warning"><i class="fas fa-user-tie"></i> Coordinator</span>';
                } elseif ($workflow->status == 'pending_dean') {
                    $workflowBadge = '<span class="badge badge-info"><i class="fas fa-user-graduate"></i> Dean</span>';
                } elseif ($workflow->status == 'approved') {
                    $workflowBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Complete</span>';
                } elseif ($workflow->status == 'rejected') {
                    $workflowBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rejected</span>';
                } else {
                    $workflowBadge = '<span class="badge badge-secondary">' . ucfirst(str_replace('_', ' ', $workflow->status)) . '</span>';
                }
            }

            // Status badge
            $statusBadge = match($submission->status) {
                'approved' => '<span class="badge badge-success">Approved</span>',
                'pending', 'pending_coordinator' => '<span class="badge badge-warning">Pending Coordinator</span>',
                'pending_dean' => '<span class="badge badge-info">Pending Dean</span>',
                'rejected' => '<span class="badge badge-danger">Rejected</span>',
                'submitted' => '<span class="badge badge-info">Submitted</span>',
                'draft' => '<span class="badge badge-secondary">Draft</span>',
                default => '<span class="badge badge-secondary">' . ucfirst(str_replace('_', ' ', $submission->status)) . '</span>'
            };

            $actions = '<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">';
            $actions .= '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.rtn-submissions.show', $submission->id) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>';

            // Check if user can approve this workflow step
            $canApprove = false;
            if ($workflow) {
                if ($workflow->assigned_to == $user->id) {
                    $canApprove = true;
                } elseif ($workflow->status == 'pending_coordinator' && $user->isResearchCoordinator()) {
                    $canApprove = true;
                } elseif ($workflow->status == 'pending_dean' && $user->isDean()) {
                    $canApprove = true;
                }
            }
            
            $canShowActions = !in_array($submission->status, ['approved', 'rejected']) 
                              && $workflow && $workflow->status !== 'approved' 
                              && $workflow->status !== 'rejected'
                              && in_array($submission->status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean'])
                              && $canApprove;
            
            if ($canShowActions) {
                $actions .= '<form action="' . route('admin.rtn-submissions.approve', $submission->id) . '" method="POST" style="display: inline;" class="approve-rtn-form">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve"><span class="material-icons-outlined">check_circle</span></button>';
                $actions .= '</form>';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-reject-rtn" title="Reject" aria-label="Reject" data-rtn-id="' . $submission->id . '"><span class="material-icons-outlined">cancel</span></button>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $submission->id,
                'title' => '<strong>' . \Str::limit($submission->title, 50) . '</strong>',
                'rtn_type' => '<span class="badge badge-info">' . strtoupper(str_replace('_', '-', $submission->rtn_type ?? 'N/A')) . '</span>',
                'user_name' => $submission->user ? $submission->user->name : 'N/A',
                'evidence_link' => $submission->evidence_link ? '<a href="' . $submission->evidence_link . '" target="_blank" class="text-primary"><i class="fas fa-link"></i> View</a>' : '<span class="text-muted">-</span>',
                'total_rtn' => $submission->total_rtn ? '<span class="badge badge-info">' . $submission->total_rtn . '</span>' : '<span class="text-muted">-</span>',
                'year' => $submission->year ?? 'N/A',
                'status' => $statusBadge,
                'workflow_status' => $workflowBadge,
                'points_allocated' => $submission->points_allocated ? '<strong style="color: var(--primary);">' . number_format($submission->points_allocated, 2) . '</strong>' : '<span class="text-muted">-</span>',
                'submitted_at' => $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'N/A',
                'actions' => $actions
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
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
        abort_if(Gate::denies('rtn_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rtnSubmission->load(['user', 'workflow', 'evidenceFiles' => function($query) {
            $query->with('uploader');
        }]);

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

            // Find workflow - create default if doesn't exist
            $workflow = ApprovalWorkflow::where('submission_type', 'rtn')
                ->where('submission_id', $rtnSubmission->id)
                ->first();

            // If no workflow exists, create a default one and submit it
            // This handles cases where RTN was submitted before workflow was created
            if (!$workflow) {
                // Create workflow in draft status
                $workflow = $this->workflowService->createWorkflow('rtn', $rtnSubmission->id, $rtnSubmission->user ?? auth()->user());
                
                // If RTN is already submitted (not draft), submit the workflow
                if (in_array($rtnSubmission->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
