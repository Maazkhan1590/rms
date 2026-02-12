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

        // If AJAX request for DataTables
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        return view('admin.grants.index');
    }

    /**
     * Get DataTables data
     */
    private function getDataTableData(Request $request)
    {
        $query = Grant::with(['submitter', 'approver', 'workflow']);

        // If current user is Faculty (non-admin), always show only their own grants
        $user = auth()->user();
        if ($user && $user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean()) {
            $query->where('submitted_by', $user->id);
        }

        // Exclude drafts by default
        $query->where('grant_status', '!=', 'draft');

        // Global search
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
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

        // Get total count before pagination
        $totalRecords = $query->count();

        // Ordering
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'title', 'grant_type', 'role', 'external_internal', 'sponsor_name', 'amount_omr', 'units', 'submitted_by', 'award_year', 'grant_status', 'workflow.status', 'points_allocated'];
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'grants.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('grants.*');
        } elseif ($orderBy === 'workflow.status') {
            $query->leftJoin('approval_workflows', function($join) {
                $join->on('grants.id', '=', 'approval_workflows.submission_id')
                     ->where('approval_workflows.submission_type', '=', 'grant');
            })
            ->orderBy('approval_workflows.status', $orderDir)
            ->select('grants.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);
        $grants = $query->skip($start)->take($length)->get();

        // Reload relationships if they were lost due to joins
        $grants->load(['submitter', 'approver', 'workflow']);

        // Format data for DataTables
        $data = $grants->map(function($grant) use ($user) {
            $workflow = $grant->workflow ?? ApprovalWorkflow::where('submission_type', 'grant')
                ->where('submission_id', $grant->id)
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

            // Status badge based on grant_status
            $statusBadge = match($grant->grant_status) {
                'approved' => '<span class="badge badge-success">Approved</span>',
                'pending' => '<span class="badge badge-warning">Pending</span>',
                'rejected' => '<span class="badge badge-danger">Rejected</span>',
                'submitted' => '<span class="badge badge-info">Submitted</span>',
                default => '<span class="badge badge-secondary">' . ucfirst($grant->grant_status ?? 'N/A') . '</span>'
            };

            $actions = '<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">';
            $actions .= '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.grants.show', $grant->id) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>';

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
            
            $canShowActions = !in_array($grant->grant_status, ['approved', 'rejected']) 
                              && $workflow && $workflow->status !== 'approved' 
                              && $workflow->status !== 'rejected'
                              && in_array($grant->grant_status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean'])
                              && $canApprove;
            
            if ($canShowActions) {
                $actions .= '<form action="' . route('admin.grants.approve', $grant->id) . '" method="POST" style="display: inline;" class="approve-grant-form">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve"><span class="material-icons-outlined">check_circle</span></button>';
                $actions .= '</form>';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-reject-grant" title="Reject" aria-label="Reject" data-grant-id="' . $grant->id . '"><span class="material-icons-outlined">cancel</span></button>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $grant->id,
                'title' => '<strong>' . \Str::limit($grant->title, 50) . '</strong>',
                'grant_type' => '<span class="badge badge-info">' . ucfirst(str_replace('_', ' ', $grant->grant_type ?? 'N/A')) . '</span>',
                'role' => '<span class="badge badge-secondary">' . strtoupper($grant->role ?? 'N/A') . '</span>',
                'external_internal' => '<span class="badge ' . ($grant->external_internal == 'External' ? 'badge-danger' : 'badge-success') . '">' . ($grant->external_internal ?? 'N/A') . '</span>',
                'sponsor_name' => \Str::limit($grant->sponsor_name ?? $grant->sponsor ?? 'N/A', 30),
                'amount_omr' => $grant->amount_omr ? '<strong>' . number_format($grant->amount_omr, 2) . ' OMR</strong>' : '<span class="text-muted">-</span>',
                'units' => $grant->units ? '<span class="badge badge-info">' . $grant->units . '</span>' : '<span class="text-muted">-</span>',
                'submitter_name' => $grant->submitter ? $grant->submitter->name : 'N/A',
                'award_year' => $grant->award_year ?? $grant->submission_year ?? 'N/A',
                'grant_status' => $statusBadge,
                'workflow_status' => $workflowBadge,
                'points_allocated' => $grant->points_allocated ? '<strong style="color: var(--primary);">' . number_format($grant->points_allocated, 2) . '</strong>' : '<span class="text-muted">-</span>',
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
                    // Update grant_status to approved
                    $grant->update([
                        'grant_status' => 'approved',
                        'status' => 'approved'
                    ]);
                    
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
                        'grant_status' => $workflow->status == 'pending_coordinator' ? 'pending' : 
                                         ($workflow->status == 'pending_dean' ? 'pending' : 'submitted'),
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
                'grant_status' => 'rejected',
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
