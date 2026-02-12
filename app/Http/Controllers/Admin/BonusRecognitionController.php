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

        // If AJAX request for DataTables
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        return view('admin.bonus-recognitions.index');
    }

    /**
     * Get DataTables data
     */
    private function getDataTableData(Request $request)
    {
        $query = BonusRecognition::with(['user', 'workflow']);

        // If current user is Faculty (non-admin), always show only their own bonus recognitions
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
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('journal_conference_name', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
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
        
        $columns = ['id', 'title', 'recognition_type', 'user_id', 'organization', 'evidence_link', 'year', 'status', 'workflow.status', 'points_allocated', 'submitted_at'];
        $orderBy = $columns[$orderColumn] ?? 'id';
        
        if ($orderBy === 'user_id') {
            $query->leftJoin('users', 'bonus_recognitions.user_id', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('bonus_recognitions.*');
        } elseif ($orderBy === 'workflow.status') {
            $query->leftJoin('approval_workflows', function($join) {
                $join->on('bonus_recognitions.id', '=', 'approval_workflows.submission_id')
                     ->where('approval_workflows.submission_type', '=', 'bonus');
            })
            ->orderBy('approval_workflows.status', $orderDir)
            ->select('bonus_recognitions.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 15);
        $recognitions = $query->skip($start)->take($length)->get();

        // Reload relationships if they were lost due to joins
        $recognitions->load(['user', 'workflow']);

        // Format data for DataTables
        $data = $recognitions->map(function($recognition) use ($user) {
            $workflow = $recognition->workflow ?? \App\Models\ApprovalWorkflow::where('submission_type', 'bonus')
                ->where('submission_id', $recognition->id)
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
            $statusBadge = match($recognition->status) {
                'approved' => '<span class="badge badge-success">Approved</span>',
                'pending', 'pending_coordinator' => '<span class="badge badge-warning">Pending Coordinator</span>',
                'pending_dean' => '<span class="badge badge-info">Pending Dean</span>',
                'rejected' => '<span class="badge badge-danger">Rejected</span>',
                'submitted' => '<span class="badge badge-info">Submitted</span>',
                'draft' => '<span class="badge badge-secondary">Draft</span>',
                default => '<span class="badge badge-secondary">' . ucfirst(str_replace('_', ' ', $recognition->status)) . '</span>'
            };

            $actions = '<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">';
            $actions .= '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.bonus-recognitions.show', $recognition->id) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>';

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
            
            $canShowActions = !in_array($recognition->status, ['approved', 'rejected']) 
                              && $workflow && $workflow->status !== 'approved' 
                              && $workflow->status !== 'rejected'
                              && in_array($recognition->status, ['pending', 'submitted', 'pending_coordinator', 'pending_dean'])
                              && $canApprove;
            
            if ($canShowActions) {
                $actions .= '<form action="' . route('admin.bonus-recognitions.approve', $recognition->id) . '" method="POST" style="display: inline;" class="approve-bonus-form">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve"><span class="material-icons-outlined">check_circle</span></button>';
                $actions .= '</form>';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-reject-bonus" title="Reject" aria-label="Reject" data-bonus-id="' . $recognition->id . '"><span class="material-icons-outlined">cancel</span></button>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $recognition->id,
                'title' => '<strong>' . \Str::limit($recognition->title, 50) . '</strong>',
                'recognition_type' => '<span class="badge badge-info">' . ucfirst(str_replace('_', ' ', $recognition->recognition_type ?? 'N/A')) . '</span>',
                'user_name' => $recognition->user ? $recognition->user->name : 'N/A',
                'organization' => \Str::limit($recognition->organization ?? 'N/A', 30),
                'evidence_link' => $recognition->evidence_link ? '<a href="' . $recognition->evidence_link . '" target="_blank" class="text-primary"><i class="fas fa-link"></i> View</a>' : '<span class="text-muted">-</span>',
                'year' => $recognition->year ?? 'N/A',
                'status' => $statusBadge,
                'workflow_status' => $workflowBadge,
                'points_allocated' => $recognition->points_allocated ? '<strong style="color: var(--primary);">' . number_format($recognition->points_allocated, 2) . '</strong>' : '<span class="text-muted">-</span>',
                'submitted_at' => $recognition->submitted_at ? $recognition->submitted_at->format('M d, Y') : 'N/A',
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

            // Find workflow - create default if doesn't exist
            $workflow = ApprovalWorkflow::where('submission_type', 'bonus')
                ->where('submission_id', $bonusRecognition->id)
                ->first();

            // If no workflow exists, create a default one and submit it
            // This handles cases where bonus recognition was submitted before workflow was created
            if (!$workflow) {
                // Create workflow in draft status
                $workflow = $this->workflowService->createWorkflow('bonus', $bonusRecognition->id, $bonusRecognition->user ?? auth()->user());
                
                // If bonus recognition is already submitted (not draft), submit the workflow
                if (in_array($bonusRecognition->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
