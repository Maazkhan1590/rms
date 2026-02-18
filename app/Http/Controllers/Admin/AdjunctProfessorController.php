<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdjunctProfessor;
use App\Models\ApprovalWorkflow;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdjunctProfessorController extends Controller
{
    protected WorkflowService $workflowService;
    protected LoggingService $loggingService;

    public function __construct(WorkflowService $workflowService, LoggingService $loggingService)
    {
        $this->workflowService = $workflowService;
        $this->loggingService = $loggingService;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('publication_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        return view('admin.adjunct-professors.index');
    }

    private function getDataTableData(Request $request)
    {
        $query = AdjunctProfessor::query();

        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'name', 'email', 'appointment_from', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        $query->orderBy($orderBy, $orderDir);

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $professors = $query->skip($start)->take($length)->get();

        $data = $professors->map(function($item) {
            $workflow = ApprovalWorkflow::where('submission_type', 'adjunct_professor')
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

            $actions = '<div class="btn-group" role="group">';
            $actions .= '<a href="' . route('admin.adjunct-professors.show', $item->id) . '" class="btn btn-sm btn-info" title="View"><i class="fa fa-eye"></i></a>';
            
            if (in_array($workflow->status ?? '', ['pending_coordinator', 'pending_dean']) && $workflow && $workflow->assigned_to == auth()->id()) {
                $actions .= '<form action="' . route('admin.adjunct-professors.approve', $item->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to approve this adjunct professor?\');">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fa fa-check"></i></button>';
                $actions .= '</form>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'name' => '<strong>' . $item->name . '</strong>',
                'email' => $item->email ?? 'N/A',
                'appointment_from' => $item->appointment_from ? $item->appointment_from->format('M d, Y') : 'N/A',
                'workflow' => $workflowBadge,
                'created' => $item->created_at ? $item->created_at->format('M d, Y') : 'N/A',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => AdjunctProfessor::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(AdjunctProfessor $adjunctProfessor)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $adjunctProfessor->load(['workflow']);
        $workflow = ApprovalWorkflow::where('submission_type', 'adjunct_professor')
            ->where('submission_id', $adjunctProfessor->id)
            ->with('assignee')
            ->first();

        return view('admin.adjunct-professors.show', compact('adjunctProfessor', 'workflow'));
    }

    public function approve(AdjunctProfessor $adjunctProfessor)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'adjunct_professor')
                ->where('submission_id', $adjunctProfessor->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('adjunct_professor', $adjunctProfessor->id, auth()->user());
            }

            $user = auth()->user();
            $canApprove = !empty($workflow)
                && !empty($workflow->assigned_to)
                && intval($workflow->assigned_to) === intval($user->id)
                && in_array($workflow->status, ['pending_coordinator', 'pending_dean'], true);
            
            if (!$canApprove) {
                \DB::rollBack();
                return redirect()->back()
                    ->with('error', 'You are not authorized to approve this adjunct professor. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'adjunct_professor_approved',
                "Approved adjunct professor: {$adjunctProfessor->name}",
                AdjunctProfessor::class,
                $adjunctProfessor->id
            );

            return redirect()->route('admin.adjunct-professors.show', $adjunctProfessor)
                ->with('success', 'Adjunct professor approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving adjunct professor: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, AdjunctProfessor $adjunctProfessor)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'adjunct_professor')
                ->where('submission_id', $adjunctProfessor->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'adjunct_professor_rejected',
                "Rejected adjunct professor: {$adjunctProfessor->name}",
                AdjunctProfessor::class,
                $adjunctProfessor->id
            );

            return redirect()->route('admin.adjunct-professors.index')
                ->with('success', 'Adjunct professor rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting adjunct professor: ' . $e->getMessage());
        }
    }

    public function destroy(AdjunctProfessor $adjunctProfessor)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $adjunctProfessor->delete();

        return redirect()->route('admin.adjunct-professors.index')
            ->with('success', 'Adjunct professor deleted successfully.');
    }
}
