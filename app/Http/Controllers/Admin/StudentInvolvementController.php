<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentInvolvement;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentInvolvementController extends Controller
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
        $categories = StudentInvolvement::distinct()->pluck('category')->filter()->sort()->values();
        $users = User::whereHas('studentInvolvements')->pluck('name', 'id');

        return view('admin.student-involvements.index', compact('statuses', 'categories', 'users'));
    }

    private function getDataTableData(Request $request)
    {
        $query = StudentInvolvement::with(['submitter', 'user', 'approver', 'workflow']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
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
                $q->where('category', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'category', 'count', 'submitted_by', 'status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'student_involvements.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('student_involvements.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $involvements = $query->skip($start)->take($length)->get();

        $data = $involvements->map(function($item) {
            $workflow = ApprovalWorkflow::where('submission_type', 'student_involvement')
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

            $actions = '<div class="btn-group" role="group">';
            $actions .= '<a href="' . route('admin.student-involvements.show', $item->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>';
            
            if (in_array($item->status, ['pending_coordinator', 'pending_dean']) && $workflow && $workflow->assigned_to == auth()->id()) {
                $actions .= '<form action="' . route('admin.student-involvements.approve', $item->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to approve this student involvement?\');">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fa fa-check"></i></button>';
                $actions .= '</form>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'category' => '<strong>' . \Str::limit($item->category, 60) . '</strong>',
                'count' => '<span class="badge badge-info">' . $item->count . '</span>',
                'submitted_by' => $item->submitter ? $item->submitter->name : 'N/A',
                'status' => $statusBadge,
                'workflow' => $workflowBadge,
                'points' => $points,
                'submitted' => $item->submitted_at ? $item->submitted_at->format('M d, Y') : 'N/A',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => StudentInvolvement::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(StudentInvolvement $involvement)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $involvement->load(['submitter', 'user', 'approver', 'evidenceFiles', 'workflow']);

        return view('admin.student-involvements.show', compact('involvement'));
    }

    public function approve(StudentInvolvement $involvement)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'student_involvement')
                ->where('submission_id', $involvement->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('student_involvement', $involvement->id, $involvement->submitter ?? auth()->user());
                
                if (in_array($involvement->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
                    ->with('error', 'You are not authorized to approve this student involvement. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $involvement->refresh();
                
                if ($workflow->status === 'approved') {
                    $points = 5.0;
                    $involvement->points_allocated = $points;
                    $involvement->points_locked = true;
                    $involvement->save();
                    
                    if ($involvement->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $involvement->submitted_by,
                            $involvement->academic_year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'student_involvement_approved',
                "Approved student involvement: {$involvement->category}",
                StudentInvolvement::class,
                $involvement->id
            );

            return redirect()->route('admin.student-involvements.show', $involvement)
                ->with('success', 'Student involvement approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving student involvement: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, StudentInvolvement $involvement)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'student_involvement')
                ->where('submission_id', $involvement->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $involvement->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'student_involvement_rejected',
                "Rejected student involvement: {$involvement->category}",
                StudentInvolvement::class,
                $involvement->id
            );

            return redirect()->route('admin.student-involvements.index')
                ->with('success', 'Student involvement rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting student involvement: ' . $e->getMessage());
        }
    }

    public function destroy(StudentInvolvement $involvement)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $involvement->delete();

        return redirect()->route('admin.student-involvements.index')
            ->with('success', 'Student involvement deleted successfully.');
    }
}
