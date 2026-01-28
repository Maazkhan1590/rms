<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Services\ScoringService;
use App\Services\WorkflowService;
use App\Services\LoggingService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicationController extends Controller
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
     * Display a listing of publications
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('publication_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // If AJAX request for DataTables
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        // Get filter options
        $statuses = ['pending', 'submitted', 'approved', 'rejected', 'draft'];
        $years = Publication::distinct()->pluck('publication_year')->filter()->sortDesc()->values();
        $types = Publication::distinct()->pluck('publication_type')->filter()->sort()->values();
        $users = User::whereHas('publications')->pluck('name', 'id');

        return view('admin.publications.index', compact('statuses', 'years', 'types', 'users'));
    }

    /**
     * Get DataTables data
     */
    private function getDataTableData(Request $request)
    {
        $query = Publication::with(['submitter', 'primaryAuthor', 'approver']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('publication_year', $request->year);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('publication_type', $request->type);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('primary_author_id', $request->user_id);
        }

        // Global search
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('journal_name', 'like', "%{$search}%")
                  ->orWhere('conference_name', 'like', "%{$search}%")
                  ->orWhereHas('primaryAuthor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('submitter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Get total count before pagination
        $totalRecords = $query->count();

        // Ordering
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'title', 'publication_type', 'primary_author_id', 'publication_year', 'status', 'points_allocated', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';
        
        if ($orderBy === 'primary_author_id') {
            $query->leftJoin('users', 'publications.primary_author_id', '=', 'users.id')
                  ->orderBy('users.name', $orderDir)
                  ->select('publications.*');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $publications = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $publications->map(function($publication) {
            $workflow = \App\Models\ApprovalWorkflow::where('submission_type', 'publication')
                ->where('submission_id', $publication->id)
                ->first();
            
            $workflowStatus = 'No Workflow';
            $workflowBadge = '<span class="badge badge-secondary">No Workflow</span>';
            
            if ($workflow) {
                $workflowStatus = ucfirst(str_replace('_', ' ', $workflow->status));
                if ($workflow->status == 'pending_coordinator') {
                    $workflowBadge = '<span class="badge badge-warning"><i class="fas fa-user-tie"></i> Coordinator</span>';
                } elseif ($workflow->status == 'pending_dean') {
                    $workflowBadge = '<span class="badge badge-info"><i class="fas fa-user-graduate"></i> Dean</span>';
                } elseif ($workflow->status == 'approved') {
                    $workflowBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Complete</span>';
                } elseif ($workflow->status == 'rejected') {
                    $workflowBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rejected</span>';
                } else {
                    $workflowBadge = '<span class="badge badge-secondary">' . $workflowStatus . '</span>';
                }
            }

            $statusBadge = match($publication->status) {
                'approved' => '<span class="badge badge-success">Approved</span>',
                'pending' => '<span class="badge badge-warning">Pending</span>',
                'rejected' => '<span class="badge badge-danger">Rejected</span>',
                'submitted' => '<span class="badge badge-info">Submitted</span>',
                default => '<span class="badge badge-secondary">' . ucfirst($publication->status) . '</span>'
            };

            $authorName = $publication->primaryAuthor?->name ?? $publication->submitter?->name ?? 'N/A';
            $year = $publication->publication_year ?? $publication->year ?? 'N/A';
            
            $points = $publication->points_allocated 
                ? '<strong style="color: var(--primary);">' . number_format($publication->points_allocated, 2) . '</strong>' 
                    . ($publication->points_locked ? '<br><small class="text-muted"><i class="fas fa-lock"></i> Locked</small>' : '')
                : '<span class="text-muted">-</span>';

            $user = auth()->user();
            $actions = '<div style="display: flex; gap: 5px; flex-wrap: wrap;">';
            $actions .= '<a class="btn btn-sm btn-info view-btn" href="' . route('admin.publications.show', $publication->id) . '" title="View" style="padding: 4px 8px; font-size: 12px;"><i class="fas fa-eye"></i> View</a>';
            
            // Show "Submit for Approval" button for draft publications owned by the user
            if ($publication->status === 'draft' && ($publication->submitted_by == $user->id || $publication->primary_author_id == $user->id)) {
                $actions .= '<form action="' . route('publications.submit', $publication->id) . '" method="POST" style="display: inline;" onsubmit="return confirm(\'Submit this publication for approval?\');">';
                $actions .= csrf_field();
                $actions .= '<button type="submit" class="btn btn-sm btn-success" style="padding: 4px 8px; font-size: 12px;"><i class="fas fa-paper-plane"></i> Submit</button>';
                $actions .= '</form>';
            }
            
            // Check workflow status before showing approve/reject buttons
            // STRICT WORKFLOW: Only assigned users can approve (Coordinator → Dean → Approved)
            $workflow = ApprovalWorkflow::where('submission_type', 'publication')
                ->where('submission_id', $publication->id)
                ->first();
            
            // Only show approve/reject buttons if workflow exists and user is assigned to current step
            if ($workflow && in_array($workflow->status, ['pending_coordinator', 'pending_dean', 'submitted'])) {
                // Check if user is assigned to this workflow step (NO ADMIN BYPASS)
                $canApprove = false;
                
                // User must be assigned to the workflow step OR have the correct role for the step
                if ($workflow->assigned_to == $user->id) {
                    $canApprove = true; // User is assigned to this workflow step
                } elseif ($workflow->status == 'pending_coordinator' && $user->isResearchCoordinator()) {
                    $canApprove = true; // Coordinator can approve coordinator step
                } elseif ($workflow->status == 'pending_dean' && $user->isDean()) {
                    $canApprove = true; // Dean can approve dean step
                }
                // Admins CANNOT bypass workflow - they must be assigned or have coordinator/dean role
                
                if ($canApprove) {
                    $actions .= '<form action="' . route('admin.publications.approve', $publication->id) . '" method="POST" style="display: inline;" onsubmit="return confirm(\'Approve this publication at current workflow step?\');">';
                    $actions .= csrf_field();
                    $actions .= '<button type="submit" class="btn btn-sm btn-success" style="padding: 4px 8px; font-size: 12px;"><i class="fas fa-check"></i> Approve</button>';
                    $actions .= '</form>';
                    $actions .= '<button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal(' . $publication->id . ')" style="padding: 4px 8px; font-size: 12px;"><i class="fas fa-times"></i> Reject</button>';
                }
            }
            // No approve button if no workflow exists - must follow workflow process
            
            // Only show delete button to admins or the publication owner
            if ($user->isAdmin || $user->hasRole('admin') || $publication->submitted_by == $user->id) {
                $actions .= '<form action="' . route('admin.publications.destroy', $publication->id) . '" method="POST" style="display: inline;" onsubmit="return confirm(\'Are you sure?\');">';
                $actions .= csrf_field();
                $actions .= method_field('DELETE');
                $actions .= '<button type="submit" class="btn btn-sm btn-danger" style="padding: 4px 8px; font-size: 12px;"><i class="fas fa-trash"></i> Delete</button>';
                $actions .= '</form>';
            }
            $actions .= '</div>';

            return [
                'id' => $publication->id,
                'title' => '<strong>' . \Str::limit($publication->title, 60) . '</strong>' . 
                          ($publication->abstract ? '<br><small class="text-muted">' . \Str::limit(strip_tags($publication->abstract), 80) . '</small>' : ''),
                'type' => '<span class="badge badge-info">' . ucfirst(str_replace('_', ' ', $publication->publication_type ?? 'N/A')) . '</span>',
                'author' => $authorName,
                'year' => $year,
                'status' => $statusBadge,
                'workflow' => $workflowBadge,
                'points' => $points,
                'submitted' => $publication->submitted_at ? $publication->submitted_at->format('M d, Y') : 'N/A',
                'actions' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => Publication::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new publication
     * Admin cannot create publications - only faculty can submit
     */
    public function create()
    {
        return redirect()->route('publications.create')
            ->with('info', 'Please use the public publication submission form. Only faculty can submit publications.');
    }

    /**
     * Store a newly created publication
     * Admin cannot create publications - only faculty can submit
     */
    public function store(Request $request)
    {
        return redirect()->route('publications.create')
            ->with('info', 'Please use the public publication submission form. Only faculty can submit publications.');
    }

    /**
     * Display the specified publication
     */
    public function show(Publication $publication)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $publication->load(['submitter', 'primaryAuthor', 'approver', 'evidenceFiles', 'workflow']);

        return view('admin.publications.show', compact('publication'));
    }

    /**
     * Show the form for editing the specified publication
     * Admin cannot edit publications - only approve/reject
     */
    public function edit(Publication $publication)
    {
        return redirect()->route('admin.publications.show', $publication)
            ->with('info', 'Admin users can only approve or reject publications. Editing is not allowed.');
    }

    /**
     * Update the specified publication
     * Admin cannot edit publications - only approve/reject
     */
    public function update(Request $request, Publication $publication)
    {
        return redirect()->route('admin.publications.show', $publication)
            ->with('info', 'Admin users can only approve or reject publications. Editing is not allowed.');
    }

    /**
     * Remove the specified publication
     */
    public function destroy(Publication $publication)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $publication->delete();

        return redirect()->route('admin.publications.index')
            ->with('success', 'Publication deleted successfully.');
    }

    /**
     * Mass destroy publications
     */
    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('publication_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:publications,id',
        ]);

        Publication::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.publications.index')
            ->with('success', 'Publications deleted successfully.');
    }

    /**
     * Approve publication
     * Integrates with workflow and scoring systems
     */
    public function approve(Publication $publication)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            \DB::beginTransaction();

            // Find workflow - MUST exist for approval
            $workflow = ApprovalWorkflow::where('submission_type', 'publication')
                ->where('submission_id', $publication->id)
                ->first();

            // STRICT WORKFLOW: Workflow must exist - no admin bypass
            if (!$workflow) {
                \DB::rollBack();
                return redirect()->back()
                    ->with('error', 'No workflow found for this publication. The publication must be submitted through the proper workflow process.');
            }

            // Check if user can approve this workflow step (STRICT - NO ADMIN BYPASS)
            $user = auth()->user();
            $canApprove = false;
            
            // User must be assigned to the workflow step OR have the correct role for the step
            if ($workflow->assigned_to == $user->id) {
                $canApprove = true; // User is assigned to this workflow step
            } elseif ($workflow->status == 'pending_coordinator' && $user->isResearchCoordinator()) {
                $canApprove = true; // Coordinator can approve coordinator step
            } elseif ($workflow->status == 'pending_dean' && $user->isDean()) {
                $canApprove = true; // Dean can approve dean step
            }
            // Admins CANNOT bypass workflow - they must be assigned or have coordinator/dean role
            
            if (!$canApprove) {
                \DB::rollBack();
                return redirect()->back()
                    ->with('error', 'You are not authorized to approve this publication. Only the assigned Coordinator or Dean can approve at the current workflow step.');
            }

            if ($workflow) {
                
                // Use workflow service to approve
                $workflow = $this->workflowService->approveWorkflow($workflow, auth()->user(), 'Approved at workflow step');
                
                // Refresh workflow to get latest status
                $workflow->refresh();
                
                // Refresh publication to get any updates from finalizeApproval
                $publication->refresh();
                
                // If workflow is fully approved, finalizeApproval already updated the publication
                // But we need to calculate points (finalizeApproval may have locked points, so unlock temporarily)
                if ($workflow->status === 'approved') {
                    // Temporarily unlock points if locked by finalizeApproval
                    $wasLocked = $publication->points_locked;
                    if ($wasLocked) {
                        $publication->points_locked = false;
                        $publication->save();
                    }
                    
                    // Calculate and assign points using scoring service
                    $points = $this->scoringService->calculatePublicationPoints($publication->fresh());
                    
                    // Lock points after calculation
                    $publication->points_locked = true;
                    $publication->save();
                    
                    // Recalculate user's total points
                    if ($publication->primary_author_id) {
                        $this->scoringService->recalculateUserTotalPoints(
                            $publication->primary_author_id,
                            $publication->publication_year ?? $publication->year
                        );
                    }
                } else {
                    // Workflow still in progress - update status based on workflow status
                    $publication->update([
                        'status' => $workflow->status == 'pending_coordinator' ? 'pending_coordinator' : 
                                   ($workflow->status == 'pending_dean' ? 'pending_dean' : 'submitted'),
                    ]);
                }
            }

            \DB::commit();

            // Log audit
            $oldValues = ['status' => $publication->getOriginal('status')];
            $newValues = ['status' => $publication->status, 'approved_at' => $publication->approved_at];
            $this->loggingService->logAudit('publication.approved', $publication, $oldValues, $newValues);

            // Log activity
            $this->loggingService->logActivity(
                'publication_approved',
                "Approved publication: {$publication->title}",
                Publication::class,
                $publication->id
            );

            $message = $workflow && $workflow->status !== 'approved' 
                ? 'Publication approved at current workflow step.'
                : 'Publication approved successfully. Points allocated: ' . ($publication->points_allocated ?? 0);

            return redirect()->route('admin.publications.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error approving publication: ' . $e->getMessage());
            
            return redirect()->route('admin.publications.index')
                ->with('error', 'Error approving publication: ' . $e->getMessage());
        }
    }

    /**
     * Reject publication
     * Integrates with workflow system
     */
    public function reject(Request $request, Publication $publication)
    {
        abort_if(Gate::denies('publication_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Find workflow if exists
            $workflow = ApprovalWorkflow::where('submission_type', 'publication')
                ->where('submission_id', $publication->id)
                ->first();

            if ($workflow) {
                // Use workflow service to reject
                $this->workflowService->rejectWorkflow($workflow, auth()->user(), $request->reason ?? 'Rejected by admin');
            }

            // Update publication status
            $oldStatus = $publication->status;
            $publication->update([
                'status' => 'rejected',
                'approver_id' => auth()->id(),
            ]);

            \DB::commit();

            // Log audit
            $oldValues = ['status' => $oldStatus];
            $newValues = ['status' => $publication->status];
            $this->loggingService->logAudit('publication.rejected', $publication, $oldValues, $newValues);

            // Log activity
            $this->loggingService->logActivity(
                'publication_rejected',
                "Rejected publication: {$publication->title}",
                Publication::class,
                $publication->id
            );

            return redirect()->route('admin.publications.index')
                ->with('success', 'Publication rejected successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error rejecting publication: ' . $e->getMessage());
            
            return redirect()->route('admin.publications.index')
                ->with('error', 'Error rejecting publication: ' . $e->getMessage());
        }
    }
}
