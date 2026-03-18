<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConferenceActivity;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConferenceActivityController extends Controller
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
        $years = ConferenceActivity::distinct()->pluck('year')->filter()->sortDesc()->values();
        $types = ConferenceActivity::distinct()->pluck('activity_type')->filter()->sort()->values();
        $users = User::whereHas('conferenceActivities')->pluck('name', 'id');

        return view('admin.conference-activities.index', compact('statuses', 'years', 'types', 'users'));
    }

    private function getDataTableData(Request $request)
    {
        $query = ConferenceActivity::with(['submitter', 'user', 'approver', 'workflow']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        if ($request->has('type') && $request->type) {
            $query->where('activity_type', $request->type);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('submitted_by', $request->user_id);
        }

        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('conference', 'like', "%{$search}%")
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = $query->count();

        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'conference', 'activity_type', 'submitted_by', 'year', 'status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'submitted_by') {
            $query->leftJoin('users', 'conference_activities.submitted_by', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('conference_activities.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $activities = $query->skip($start)->take($length)->get();

        $data = $activities->map(function($item) {
            $workflow = ApprovalWorkflow::where('submission_type', 'conference_activity')
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
            $actions .= '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.conference-activities.show', $item->id) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>';
            
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
                $actions .= '<form action="' . route('admin.conference-activities.approve', $item->id) . '" method="POST" style="display: inline;" class="approve-submission-form">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-outline-success" title="Approve" aria-label="Approve"><span class="material-icons-outlined">check_circle</span></button>';
                $actions .= '</form>';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-reject-submission" title="Reject" aria-label="Reject" data-reject-url="' . route('admin.conference-activities.reject', $item->id) . '"><span class="material-icons-outlined">cancel</span></button>';
            }
            
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'conference' => '<strong>' . \Str::limit($item->conference, 60) . '</strong>',
                'activity_type' => '<span class="badge badge-info">' . ucfirst($item->activity_type ?? 'N/A') . '</span>',
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
            'recordsTotal' => ConferenceActivity::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function show(ConferenceActivity $activity)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity->load(['submitter', 'user', 'approver']);

        $workflow = ApprovalWorkflow::where('submission_type', 'conference_activity')
            ->where('submission_id', $activity->id)
            ->with(['submitter', 'assignee', 'history.performer'])
            ->first();

        $evidenceFiles = \App\Models\EvidenceFile::where('submission_type', 'conference_activity')
            ->where('submission_id', $activity->id)
            ->with('uploader')
            ->get();

        $activity->setRelation('workflow', $workflow);
        $activity->setRelation('evidenceFiles', $evidenceFiles);

        return view('admin.conference-activities.show', compact('activity'));
    }

    public function approve(ConferenceActivity $activity)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'conference_activity')
                ->where('submission_id', $activity->id)
                ->first();

            if (!$workflow) {
                $workflow = $this->workflowService->createWorkflow('conference_activity', $activity->id, $activity->submitter ?? auth()->user());
                
                if (in_array($activity->status, ['submitted', 'pending_coordinator', 'pending_dean'])) {
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
                    ->with('error', 'You are not authorized to approve this conference activity. Only the currently assigned approver can approve at the current workflow step.');
            }

            if ($workflow) {
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                $workflow->refresh();
                $activity->refresh();
                
                if ($workflow->status === 'approved') {
                    $points = 5.0;
                    $activity->points_allocated = $points;
                    $activity->points_locked = true;
                    $activity->save();
                    
                    if ($activity->submitted_by) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $activity->submitted_by,
                            $activity->year ?? date('Y')
                        );
                    }
                }
            }

            \DB::commit();

            $this->loggingService->logActivity(
                'conference_activity_approved',
                "Approved conference activity: {$activity->conference}",
                ConferenceActivity::class,
                $activity->id
            );

            return redirect()->route('admin.conference-activities.show', $activity)
                ->with('success', 'Conference activity approved successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error approving conference activity: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, ConferenceActivity $activity)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'comments' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $workflow = ApprovalWorkflow::where('submission_type', 'conference_activity')
                ->where('submission_id', $activity->id)
                ->first();

            if ($workflow) {
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->comments ?? 'Rejected');
            }

            $activity->update([
                'status' => 'rejected',
            ]);

            \DB::commit();

            $this->loggingService->logActivity(
                'conference_activity_rejected',
                "Rejected conference activity: {$activity->conference}",
                ConferenceActivity::class,
                $activity->id
            );

            return redirect()->route('admin.conference-activities.index')
                ->with('success', 'Conference activity rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error rejecting conference activity: ' . $e->getMessage());
        }
    }

    public function destroy(ConferenceActivity $activity)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activity->delete();

        return redirect()->route('admin.conference-activities.index')
            ->with('success', 'Conference activity deleted successfully.');
    }
}
