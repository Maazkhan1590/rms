<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupervisionExam;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupervisionExamController extends Controller
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
        $roles = SupervisionExam::distinct()->pluck('role')->filter()->sort()->values();
        $degrees = SupervisionExam::distinct()->pluck('degree')->filter()->sort()->values();
        $users = User::whereHas('supervisionExams')->pluck('name', 'id');

        return view('admin.supervision-exams.index', compact('statuses', 'roles', 'degrees', 'users'));
    }

    private function getDataTableData(Request $request)
    {
        $query = SupervisionExam::with(['submitter', 'user', 'approver', 'workflow']);

        if ($request->has('status') && $request->status) {
            $query->where('workflow_status', $request->status);
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('degree') && $request->degree) {
            $query->where('degree', $request->degree);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('submitted_by', $request->user_id);
        }

        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('thesis_title', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'student_name', 'role', 'submitted_by', 'degree', 'workflow_status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'supervision_exams.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('supervision_exams.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $supervisions = $query->skip($start)->take($length)->get();

        $data = $supervisions->map(function($item) {
            $workflow = ApprovalWorkflow::where('submission_type', 'supervision_exam')
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
            $actions .= '<a href="' . route('admin.supervision-exams.show', $item->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>';
            
            if (in_array($item->workflow_status, ['pending_coordinator', 'pending_dean']) && $workflow && $workflow->assigned_to == auth()->id()) {
                $actions .= '<form action="' . route('admin.supervision-exams.approve', $item->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to approve this supervision/exam record?\');">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fa fa-check"></i></button>';
                $actions .= '</form>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'student_name' => '<strong>' . \Str::limit($item->student_name, 60) . '</strong>',
                'role' => '<span class="badge badge-info">' . ucfirst($item->role ?? 'N/A') . '</span>',
                'submitted_by' => $item->submitter ? $item->submitter->name : 'N/A',
                'degree' => $item->degree ?? 'N/A',
                'status' => $statusBadge,
                'workflow' => $workflowBadge,
                'points' => $points,
                'submitted' => $item->submitted_at ? $item->submitted_at->format('M d, Y') : 'N/A',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => SupervisionExam::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(SupervisionExam $supervision)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervision->load(['submitter', 'user', 'approver', 'evidenceFiles', 'workflow']);

        return view('admin.supervision-exams.show', compact('supervision'));
    }

    public function approve(SupervisionExam $supervision)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'supervision_exam')
                ->where('submission_id', $supervision->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('supervision_exam', $supervision->id, $supervision->submitter ?? auth()->user());
                
                if (in_array($supervision->workflow_status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
                    ->with('error', 'You are not authorized to approve this supervision/exam record. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $supervision->refresh();
                
                if ($workflow->status === 'approved') {
                    $points = 5.0;
                    $supervision->points_allocated = $points;
                    $supervision->points_locked = true;
                    $supervision->save();
                    
                    if ($supervision->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $supervision->submitted_by,
                            $supervision->start_year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'supervision_exam_approved',
                "Approved supervision/exam: {$supervision->student_name}",
                SupervisionExam::class,
                $supervision->id
            );

            return redirect()->route('admin.supervision-exams.show', $supervision)
                ->with('success', 'Supervision/Exam record approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving supervision/exam record: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, SupervisionExam $supervision)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'supervision_exam')
                ->where('submission_id', $supervision->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $supervision->update([
                'workflow_status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'supervision_exam_rejected',
                "Rejected supervision/exam: {$supervision->student_name}",
                SupervisionExam::class,
                $supervision->id
            );

            return redirect()->route('admin.supervision-exams.index')
                ->with('success', 'Supervision/Exam record rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting supervision/exam record: ' . $e->getMessage());
        }
    }

    public function destroy(SupervisionExam $supervision)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $supervision->delete();

        return redirect()->route('admin.supervision-exams.index')
            ->with('success', 'Supervision/Exam record deleted successfully.');
    }
}
