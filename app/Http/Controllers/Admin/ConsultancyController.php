<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultancy;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsultancyController extends Controller
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

        $statuses = ['draft', 'submitted', 'pending_coordinator', 'pending_dean', 'approved', 'rejected', 'returned', 'ongoing', 'completed'];
        $years = Consultancy::distinct()->pluck('year')->filter()->sortDesc()->values();
        $types = Consultancy::distinct()->pluck('income_type')->filter()->sort()->values();
        $users = User::whereHas('consultancies')->pluck('name', 'id');

        return view('admin.consultancies.index', compact('statuses', 'years', 'types', 'users'));
    }

    private function getDataTableData(Request $request)
    {
        $query = Consultancy::with(['submitter', 'user', 'approver', 'workflow']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        if ($request->has('type') && $request->type) {
            $query->where('income_type', $request->type);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('submitted_by', $request->user_id);
        }

        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('project_consultancy_name', 'like', "%{$search}%")
                  ->orWhere('client_sponsor', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'project_consultancy_name', 'income_type', 'submitted_by', 'year', 'status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'consultancies_kts.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('consultancies_kts.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $consultancies = $query->skip($start)->take($length)->get();

        $user = auth()->user();

        $data = $consultancies->map(function($item) use ($user) {
            $workflow = ApprovalWorkflow::where('submission_type', 'consultancy')
                ->where('submission_id', $item->id)
                ->with('assignee')
                ->first();
            
            $workflowBadge = '<span class="badge badge-secondary">No Workflow</span>';
            
            if ($workflow) {
                if ($workflow->status == 'pending_coordinator') {
                    $workflowBadge = '<span class="badge badge-warning"><i class="fas fa-user-tie"></i> Coordinator</span>';
                } elseif ($workflow->status == 'pending_dean') {
                    $workflowBadge = '<span class="badge badge-info"><i class="fas fa-user-graduate"></i> Dean</span>';
                } elseif ($workflow->status == 'submitted') {
                    $workflowBadge = '<span class="badge badge-secondary">Submitted</span>';
                } elseif ($workflow->status == 'approved') {
                    $workflowBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Complete</span>';
                } elseif ($workflow->status == 'rejected') {
                    $workflowBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rejected</span>';
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
                'ongoing' => '<span class="badge badge-info">Ongoing</span>',
                'completed' => '<span class="badge badge-success">Completed</span>',
                default => '<span class="badge badge-secondary">' . ucfirst($item->status) . '</span>',
            };

            $points = $item->points_allocated ? number_format($item->points_allocated, 2) : '0.00';
            $year = $item->year ?? ($item->start_date ? $item->start_date->format('Y') : 'N/A');

            $actions = '<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">';
            $actions .= '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.consultancies.show', $item->id) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>';

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

            $canShowActions = !in_array($item->status, ['approved', 'rejected'])
                              && $workflow && $workflow->status !== 'approved'
                              && $workflow->status !== 'rejected'
                              && in_array($item->status, ['pending_coordinator', 'pending_dean', 'submitted'])
                              && $canApprove;

            if ($canShowActions) {
                $actions .= '<form action="' . route('admin.consultancies.approve', $item->id) . '" method="POST" style="display: inline;" class="approve-consultancy-form">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve"><span class="material-icons-outlined">check_circle</span></button>';
                $actions .= '</form>';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-reject-consultancy" title="Reject" aria-label="Reject" data-consultancy-id="' . $item->id . '"><span class="material-icons-outlined">cancel</span></button>';
            }

            $actions .= '</div>';

            return [
                'id' => $item->id,
                'project_consultancy_name' => '<strong>' . \Str::limit($item->project_consultancy_name, 60) . '</strong>',
                'income_type' => '<span class="badge badge-info">' . ucfirst($item->income_type ?? 'N/A') . '</span>',
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
            'recordsTotal' => Consultancy::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(Consultancy $consultancy)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultancy->load(['submitter', 'user', 'approver', 'evidenceFiles', 'workflow']);

        return view('admin.consultancies.show', compact('consultancy'));
    }

    public function approve(Consultancy $consultancy)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'consultancy')
                ->where('submission_id', $consultancy->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('consultancy', $consultancy->id, $consultancy->submitter ?? auth()->user());
                
                if (in_array($consultancy->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
                    ->with('error', 'You are not authorized to approve this consultancy. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $consultancy->refresh();
                
                if ($workflow->status === 'approved') {
                    $points = 5.0;
                    $consultancy->points_allocated = $points;
                    $consultancy->points_locked = true;
                    $consultancy->save();
                    
                    if ($consultancy->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $consultancy->submitted_by,
                            $consultancy->year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'consultancy_approved',
                "Approved consultancy/KT: {$consultancy->project_consultancy_name}",
                Consultancy::class,
                $consultancy->id
            );

            return redirect()->route('admin.consultancies.show', $consultancy)
                ->with('success', 'Consultancy/KT approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving consultancy: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Consultancy $consultancy)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'consultancy')
                ->where('submission_id', $consultancy->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $consultancy->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'consultancy_rejected',
                "Rejected consultancy/KT: {$consultancy->project_consultancy_name}",
                Consultancy::class,
                $consultancy->id
            );

            return redirect()->route('admin.consultancies.index')
                ->with('success', 'Consultancy/KT rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting consultancy: ' . $e->getMessage());
        }
    }

    public function destroy(Consultancy $consultancy)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $consultancy->delete();

        return redirect()->route('admin.consultancies.index')
            ->with('success', 'Consultancy/KT deleted successfully.');
    }
}
