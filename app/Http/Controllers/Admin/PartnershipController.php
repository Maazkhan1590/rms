<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnershipMou;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnershipController extends Controller
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
     * Display a listing of partnerships/MOUs
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('publication_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // If AJAX request for DataTables
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        // Get filter options
        $statuses = ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned'];
        $years = PartnershipMou::distinct()->pluck('year')->filter()->sortDesc()->values();
        $types = PartnershipMou::distinct()->pluck('type')->filter()->sort()->values();
        $users = User::whereHas('partnerships')->pluck('name', 'id');

        return view('admin.partnerships.index', compact('statuses', 'years', 'types', 'users'));
    }

    /**
     * Get DataTables data
     */
    private function getDataTableData(Request $request)
    {
        $query = PartnershipMou::with(['submitter', 'leadStaff', 'approver', 'workflow']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('submitted_by', $request->user_id);
        }

        // Global search
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('partner_organization', 'like', "%{$search}%")
                  ->orWhere('scope_theme', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('leadStaff', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Get total count before pagination
        $totalRecords = $query->count();

        // Ordering
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'partner_organization', 'type', 'submitted_by', 'year', 'status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'partnerships_mous.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('partnerships_mous.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $partnerships = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $partnerships->map(function($partnership) {
            $workflow = ApprovalWorkflow::where('submission_type', 'mou')
                ->where('submission_id', $partnership->id)
                ->with('assignee')
                ->first();
            
            $workflowBadge = '<span class="badge badge-secondary">No Workflow</span>';
            
            if ($workflow) {
                if ($workflow->status == 'pending_coordinator') {
                    $assignedUser = $workflow->assignee ? $workflow->assignee->name : 'Unassigned';
                    $workflowBadge = '<span class="badge badge-warning">Pending Coordinator</span>';
                } elseif ($workflow->status == 'pending_dean') {
                    $assignedUser = $workflow->assignee ? $workflow->assignee->name : 'Unassigned';
                    $workflowBadge = '<span class="badge badge-info">Pending Dean</span>';
                } elseif ($workflow->status == 'submitted') {
                    $workflowBadge = '<span class="badge badge-secondary">Submitted</span>';
                } elseif ($workflow->status == 'approved') {
                    $workflowBadge = '<span class="badge badge-success">Approved</span>';
                } elseif ($workflow->status == 'rejected') {
                    $workflowBadge = '<span class="badge badge-danger">Rejected</span>';
                }
            }

            $statusBadge = match($partnership->status) {
                'draft' => '<span class="badge badge-secondary">Draft</span>',
                'submitted' => '<span class="badge badge-info">Submitted</span>',
                'pending_coordinator' => '<span class="badge badge-warning">Pending Coordinator</span>',
                'pending_dean' => '<span class="badge badge-info">Pending Dean</span>',
                'approved' => '<span class="badge badge-success">Approved</span>',
                'rejected' => '<span class="badge badge-danger">Rejected</span>',
                'returned' => '<span class="badge badge-warning">Returned</span>',
                default => '<span class="badge badge-secondary">' . ucfirst($partnership->status) . '</span>',
            };

            $points = $partnership->points_allocated ? number_format($partnership->points_allocated, 2) : '0.00';
            $year = $partnership->year ?? ($partnership->date_signed ? $partnership->date_signed->format('Y') : 'N/A');

            $actions = '<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">';
            $actions .= '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.partnerships.show', $partnership->id) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>';
            
            $canApprove = false;
            if ($workflow) {
                if ($workflow->assigned_to == auth()->id()) {
                    $canApprove = true;
                } elseif ($workflow->status == 'pending_coordinator' && auth()->user()->isResearchCoordinator()) {
                    $canApprove = true;
                } elseif ($workflow->status == 'pending_dean' && auth()->user()->isDean()) {
                    $canApprove = true;
                }
            }

            $canShowActions = !in_array($partnership->status, ['approved', 'rejected'])
                && $workflow && $workflow->status !== 'approved'
                && $workflow->status !== 'rejected'
                && in_array($partnership->status, ['pending_coordinator', 'pending_dean', 'submitted'])
                && $canApprove;

            if ($canShowActions) {
                $actions .= '<form action="' . route('admin.partnerships.approve', $partnership->id) . '" method="POST" style="display: inline;" class="approve-submission-form">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve"><span class="material-icons-outlined">check_circle</span></button>';
                $actions .= '</form>';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-reject-submission" title="Reject" aria-label="Reject" data-reject-url="' . route('admin.partnerships.reject', $partnership->id) . '"><span class="material-icons-outlined">cancel</span></button>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $partnership->id,
                'partner_organization' => '<strong>' . \Str::limit($partnership->partner_organization, 60) . '</strong>',
                'type' => '<span class="badge badge-info">' . strtoupper($partnership->type ?? 'N/A') . '</span>',
                'submitted_by' => $partnership->submitter ? $partnership->submitter->name : 'N/A',
                'year' => $year,
                'status' => $statusBadge,
                'workflow' => $workflowBadge,
                'points' => $points,
                'submitted' => $partnership->submitted_at ? $partnership->submitted_at->format('M d, Y') : 'N/A',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => PartnershipMou::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    /**
     * Display the specified partnership/MOU
     */
    public function show(PartnershipMou $partnership)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $partnership->load(['submitter', 'leadStaff', 'approver']);

        $workflow = ApprovalWorkflow::where('submission_type', 'mou')
            ->where('submission_id', $partnership->id)
            ->with(['submitter', 'assignee', 'history.performer'])
            ->first();

        $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'mou')
            ->where('submission_id', $partnership->id)
            ->with('uploader')
            ->get();

        $partnership->setRelation('workflow', $workflow);
        $partnership->setRelation('evidenceFiles', $evidenceFiles);

        return view('admin.partnerships.show', compact('partnership'));
    }

    /**
     * Approve partnership/MOU
     */
    public function approve(PartnershipMou $partnership)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'mou')
                ->where('submission_id', $partnership->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('mou', $partnership->id, $partnership->submitter ?? auth()->user());
                
                if (in_array($partnership->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
                    $workflow = $this->workflowService->submitWorkflow($workflow);
                }
            }

            $user = auth()->user();
            $canApprove = !empty($workflow)
                && !empty($workflow->assigned_to)
                && intval($workflow->assigned_to) === intval($user->id)
                && in_array($workflow->status, ['pending_coordinator', 'pending_dean'], true);
            
            if (!$canApprove) {
                \DB::rollBack();
                return redirect()->back()
                    ->with('error', 'You are not authorized to approve this partnership/MOU. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $partnership->refresh();
                
                if ($workflow->status === 'approved') {
                    // Calculate bonus points (default 5 points)
                    $points = 5.0;
                    $partnership->points_allocated = $points;
                    $partnership->points_locked = true;
                    $partnership->save();
                    
                    // Recalculate user's total points
                    if ($partnership->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $partnership->submitted_by,
                            $partnership->year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'partnership_approved',
                "Approved partnership/MOU: {$partnership->partner_organization}",
                PartnershipMou::class,
                $partnership->id
            );

            return redirect()->route('admin.partnerships.show', $partnership)
                ->with('success', 'Partnership/MOU approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving partnership/MOU: ' . $e->getMessage());
        }
    }

    /**
     * Reject partnership/MOU
     */
    public function reject(Request $request, PartnershipMou $partnership)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'mou')
                ->where('submission_id', $partnership->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $partnership->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'partnership_rejected',
                "Rejected partnership/MOU: {$partnership->partner_organization}",
                PartnershipMou::class,
                $partnership->id
            );

            return redirect()->route('admin.partnerships.index')
                ->with('success', 'Partnership/MOU rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting partnership/MOU: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified partnership/MOU
     */
    public function destroy(PartnershipMou $partnership)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $partnership->delete();

        return redirect()->route('admin.partnerships.index')
            ->with('success', 'Partnership/MOU deleted successfully.');
    }
}
