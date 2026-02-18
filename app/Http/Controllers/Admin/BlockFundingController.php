<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockFunding;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockFundingController extends Controller
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

    public function index(Request $request)
    {
        abort_if(Gate::denies('publication_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        $statuses = ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned', 'active', 'completed'];
        $years = BlockFunding::distinct()->pluck('year')->filter()->sortDesc()->values();
        $users = User::whereHas('blockFundings')->pluck('name', 'id');

        return view('admin.block-fundings.index', compact('statuses', 'years', 'users'));
    }

    private function getDataTableData(Request $request)
    {
        $query = BlockFunding::with(['submitter', 'user', 'approver', 'workflow']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('submitted_by', $request->user_id);
        }

        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('project_title', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'project_title', 'submitted_by', 'year', 'status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'block_fundings.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('block_fundings.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $fundings = $query->skip($start)->take($length)->get();

        $data = $fundings->map(function($item) {
            $workflow = ApprovalWorkflow::where('submission_type', 'block_funding')
                ->where('submission_id', $item->id)
                ->with('assignee')
                ->first();
            
            $workflowBadge = '<span class="badge badge-secondary">No Workflow</span>';
            
            if ($workflow) {
                if ($workflow->status == 'pending_coordinator') {
                    $workflowBadge = '<span class="badge badge-warning">Pending Coordinator</span>';
                } elseif ($workflow->status == 'pending_dean') {
                    $workflowBadge = '<span class="badge badge-info">Pending Dean</span>';
                } elseif ($workflow->status == 'submitted') {
                    $workflowBadge = '<span class="badge badge-secondary">Submitted</span>';
                } elseif ($workflow->status == 'approved') {
                    $workflowBadge = '<span class="badge badge-success">Approved</span>';
                } elseif ($workflow->status == 'rejected') {
                    $workflowBadge = '<span class="badge badge-danger">Rejected</span>';
                }
            }

            $statusBadge = match($item->status) {
                'draft' => '<span class="badge badge-secondary">Draft</span>',
                'submitted' => '<span class="badge badge-info">Submitted</span>',
                'pending_coordinator' => '<span class="badge badge-warning">Pending Coordinator</span>',
                'pending_dean' => '<span class="badge badge-info">Pending Dean</span>',
                'approved' => '<span class="badge badge-success">Approved</span>',
                'rejected' => '<span class="badge badge-danger">Rejected</span>',
                'returned' => '<span class="badge badge-warning">Returned</span>',
                'active' => '<span class="badge badge-info">Active</span>',
                'completed' => '<span class="badge badge-success">Completed</span>',
                default => '<span class="badge badge-secondary">' . ucfirst($item->status) . '</span>',
            };

            $points = $item->points_allocated ? number_format($item->points_allocated, 2) : '0.00';
            $year = $item->year ?? ($item->start_date ? $item->start_date->format('Y') : 'N/A');

            $actions = '<div class="btn-group" role="group">';
            $actions .= '<a href="' . route('admin.block-fundings.show', $item->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>';
            
            if (in_array($item->status, ['pending_coordinator', 'pending_dean']) && $workflow && $workflow->assigned_to == auth()->id()) {
                $actions .= '<form action="' . route('admin.block-fundings.approve', $item->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to approve this block funding?\');">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fa fa-check"></i></button>';
                $actions .= '</form>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'project_title' => '<strong>' . \Str::limit($item->project_title, 60) . '</strong>',
                'funding_source' => $item->funding_source ?? 'N/A',
                'submitted_by' => $item->submitter ? $item->submitter->name : 'N/A',
                'year' => $year,
                'status' => $statusBadge,
                'workflow' => $workflowBadge,
                'points' => $points,
                'submitted' => $item->submitted_at ? $item->submitted_at->format('M d, Y') : 'N/A',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => BlockFunding::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(BlockFunding $funding)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $funding->load(['submitter', 'user', 'approver', 'evidenceFiles', 'workflow']);

        return view('admin.block-fundings.show', compact('funding'));
    }

    public function approve(BlockFunding $funding)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'block_funding')
                ->where('submission_id', $funding->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('block_funding', $funding->id, $funding->submitter ?? auth()->user());
                
                if (in_array($funding->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
                    ->with('error', 'You are not authorized to approve this block funding. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $funding->refresh();
                
                if ($workflow->status === 'approved') {
                    $points = 5.0;
                    $funding->points_allocated = $points;
                    $funding->points_locked = true;
                    $funding->save();
                    
                    if ($funding->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $funding->submitted_by,
                            $funding->year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'block_funding_approved',
                "Approved block funding: {$funding->project_title}",
                BlockFunding::class,
                $funding->id
            );

            return redirect()->route('admin.block-fundings.show', $funding)
                ->with('success', 'Block funding approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving block funding: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, BlockFunding $funding)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'block_funding')
                ->where('submission_id', $funding->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $funding->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'block_funding_rejected',
                "Rejected block funding: {$funding->project_title}",
                BlockFunding::class,
                $funding->id
            );

            return redirect()->route('admin.block-fundings.index')
                ->with('success', 'Block funding rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting block funding: ' . $e->getMessage());
        }
    }

    public function destroy(BlockFunding $funding)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $funding->delete();

        return redirect()->route('admin.block-fundings.index')
            ->with('success', 'Block funding deleted successfully.');
    }
}
