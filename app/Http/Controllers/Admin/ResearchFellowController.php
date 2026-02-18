<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResearchFellow;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResearchFellowController extends Controller
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

        $statuses = ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned'];
        $years = ResearchFellow::distinct()->pluck('year')->filter()->sortDesc()->values();
        $users = User::whereHas('researchFellows')->pluck('name', 'id');

        return view('admin.research-fellows.index', compact('statuses', 'years', 'users'));
    }

    private function getDataTableData(Request $request)
    {
        $query = ResearchFellow::with(['submitter', 'user', 'approver', 'workflow']);

        if ($request->has('status') && $request->status) {
            $query->where('workflow_status', $request->status);
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
                $q->where('publication_title', 'like', "%{$search}%")
                  ->orWhere('journal', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'publication_title', 'submitted_by', 'year', 'workflow_status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'research_fellows.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('research_fellows.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $fellows = $query->skip($start)->take($length)->get();

        $data = $fellows->map(function($item) {
            $workflow = ApprovalWorkflow::where('submission_type', 'research_fellow')
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

            $statusBadge = match($item->workflow_status) {
                'draft' => '<span class="badge badge-secondary">Draft</span>',
                'submitted' => '<span class="badge badge-info">Submitted</span>',
                'pending_coordinator' => '<span class="badge badge-warning">Pending Coordinator</span>',
                'pending_dean' => '<span class="badge badge-info">Pending Dean</span>',
                'approved' => '<span class="badge badge-success">Approved</span>',
                'rejected' => '<span class="badge badge-danger">Rejected</span>',
                'returned' => '<span class="badge badge-warning">Returned</span>',
                default => '<span class="badge badge-secondary">' . ucfirst($item->workflow_status) . '</span>',
            };

            $points = $item->points_allocated ? number_format($item->points_allocated, 2) : '0.00';

            $actions = '<div class="btn-group" role="group">';
            $actions .= '<a href="' . route('admin.research-fellows.show', $item->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>';
            
            if (in_array($item->workflow_status, ['pending_coordinator', 'pending_dean']) && $workflow && $workflow->assigned_to == auth()->id()) {
                $actions .= '<form action="' . route('admin.research-fellows.approve', $item->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to approve this research fellow?\');">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fa fa-check"></i></button>';
                $actions .= '</form>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'publication_title' => '<strong>' . \Str::limit($item->publication_title, 60) . '</strong>',
                'journal' => $item->journal ?? 'N/A',
                'submitted_by' => $item->submitter ? $item->submitter->name : 'N/A',
                'year' => $item->year ?? 'N/A',
                'status' => $statusBadge,
                'workflow' => $workflowBadge,
                'points' => $points,
                'submitted' => $item->submitted_at ? $item->submitted_at->format('M d, Y') : 'N/A',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => ResearchFellow::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(ResearchFellow $fellow)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $fellow->load(['submitter', 'user', 'approver', 'evidenceFiles', 'workflow']);

        return view('admin.research-fellows.show', compact('fellow'));
    }

    public function approve(ResearchFellow $fellow)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'research_fellow')
                ->where('submission_id', $fellow->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('research_fellow', $fellow->id, $fellow->submitter ?? auth()->user());
                
                if (in_array($fellow->workflow_status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
                    ->with('error', 'You are not authorized to approve this research fellow. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $fellow->refresh();
                
                if ($workflow->status === 'approved') {
                    $points = 5.0;
                    $fellow->points_allocated = $points;
                    $fellow->points_locked = true;
                    $fellow->save();
                    
                    if ($fellow->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $fellow->submitted_by,
                            $fellow->year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'research_fellow_approved',
                "Approved research fellow: {$fellow->publication_title}",
                ResearchFellow::class,
                $fellow->id
            );

            return redirect()->route('admin.research-fellows.show', $fellow)
                ->with('success', 'Research fellow approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving research fellow: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ResearchFellow $fellow)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'research_fellow')
                ->where('submission_id', $fellow->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $fellow->update([
                'workflow_status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'research_fellow_rejected',
                "Rejected research fellow: {$fellow->publication_title}",
                ResearchFellow::class,
                $fellow->id
            );

            return redirect()->route('admin.research-fellows.index')
                ->with('success', 'Research fellow rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting research fellow: ' . $e->getMessage());
        }
    }

    public function destroy(ResearchFellow $fellow)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $fellow->delete();

        return redirect()->route('admin.research-fellows.index')
            ->with('success', 'Research fellow deleted successfully.');
    }
}
