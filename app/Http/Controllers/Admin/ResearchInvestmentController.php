<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResearchInvestment;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResearchInvestmentController extends Controller
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
        $years = ResearchInvestment::distinct()->pluck('year')->filter()->sortDesc()->values();
        $categories = ResearchInvestment::distinct()->pluck('category')->filter()->sort()->values();
        $users = User::whereHas('researchInvestments')->pluck('name', 'id');

        return view('admin.research-investments.index', compact('statuses', 'years', 'categories', 'users'));
    }

    private function getDataTableData(Request $request)
    {
        $query = ResearchInvestment::with(['submitter', 'user', 'approver', 'workflow']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('submitted_by', $request->user_id);
        }

        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('item', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'item', 'category', 'submitted_by', 'year', 'status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'research_investments.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('research_investments.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $investments = $query->skip($start)->take($length)->get();

        $data = $investments->map(function($item) {
            $workflow = ApprovalWorkflow::where('submission_type', 'research_investment')
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
                default => '<span class="badge badge-secondary">' . ucfirst($item->status) . '</span>',
            };

            $points = $item->points_allocated ? number_format($item->points_allocated, 2) : '0.00';
            $year = $item->year ?? ($item->date ? $item->date->format('Y') : 'N/A');

            $actions = '<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: center;">';
            $actions .= '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.research-investments.show', $item->id) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>';
            
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

            $canShowActions = !in_array($item->status, ['approved', 'rejected'])
                && $workflow && $workflow->status !== 'approved'
                && $workflow->status !== 'rejected'
                && in_array($item->status, ['pending_coordinator', 'pending_dean', 'submitted'])
                && $canApprove;

            if ($canShowActions) {
                $actions .= '<form action="' . route('admin.research-investments.approve', $item->id) . '" method="POST" style="display: inline;" class="approve-submission-form">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve"><span class="material-icons-outlined">check_circle</span></button>';
                $actions .= '</form>';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-reject-submission" title="Reject" aria-label="Reject" data-reject-url="' . route('admin.research-investments.reject', $item->id) . '"><span class="material-icons-outlined">cancel</span></button>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'item' => '<strong>' . \Str::limit($item->item, 60) . '</strong>',
                'category' => '<span class="badge badge-info">' . ucfirst($item->category ?? 'N/A') . '</span>',
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
            'recordsTotal' => ResearchInvestment::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(ResearchInvestment $investment)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investment->load(['submitter', 'user', 'approver']);

        $workflow = ApprovalWorkflow::where('submission_type', 'research_investment')
            ->where('submission_id', $investment->id)
            ->with(['submitter', 'assignee', 'history.performer'])
            ->first();

        $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'research_investment')
            ->where('submission_id', $investment->id)
            ->with('uploader')
            ->get();

        $investment->setRelation('workflow', $workflow);
        $investment->setRelation('evidenceFiles', $evidenceFiles);

        return view('admin.research-investments.show', compact('investment'));
    }

    public function approve(ResearchInvestment $investment)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'research_investment')
                ->where('submission_id', $investment->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('research_investment', $investment->id, $investment->submitter ?? auth()->user());
                
                if (in_array($investment->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
                    ->with('error', 'You are not authorized to approve this research investment. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $investment->refresh();
                
                if ($workflow->status === 'approved') {
                    $points = 5.0;
                    $investment->points_allocated = $points;
                    $investment->points_locked = true;
                    $investment->save();
                    
                    if ($investment->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $investment->submitted_by,
                            $investment->year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'research_investment_approved',
                "Approved research investment: {$investment->item}",
                ResearchInvestment::class,
                $investment->id
            );

            return redirect()->route('admin.research-investments.show', $investment)
                ->with('success', 'Research investment approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving research investment: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ResearchInvestment $investment)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'research_investment')
                ->where('submission_id', $investment->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $investment->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'research_investment_rejected',
                "Rejected research investment: {$investment->item}",
                ResearchInvestment::class,
                $investment->id
            );

            return redirect()->route('admin.research-investments.index')
                ->with('success', 'Research investment rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting research investment: ' . $e->getMessage());
        }
    }

    public function destroy(ResearchInvestment $investment)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investment->delete();

        return redirect()->route('admin.research-investments.index')
            ->with('success', 'Research investment deleted successfully.');
    }
}
