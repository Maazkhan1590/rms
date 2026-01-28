<?php

namespace App\Services;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalHistory;
use App\Models\WorkflowAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Workflow Service
 * 
 * Approval Workflow Configuration:
 * 
 * Default Workflow: Faculty → Research Coordinator → Dean → Approved
 * - Step 1: Faculty submits (draft)
 * - Step 2: Coordinator reviews (pending_coordinator)
 * - Step 3: Dean reviews (pending_dean)
 * - Step 4: Approved
 * 
 * Fallback Workflow: Faculty → Dean → Approved (if no coordinator assigned)
 * - If no Research Coordinator is found, workflow skips to Dean
 * - fallback_used flag is set to true
 * 
 * Auto-escalation: If approver unavailable, workflows can be manually reassigned by Admin
 * - Admins can use reassignWorkflow() method to manually assign workflows
 * - Auto-escalation runs after 7 days of pending status
 * 
 * Scope: Assignments can be college/department-specific or global (leave empty for all)
 * - NULL college/department = Global scope (applies to all)
 * - Specific college/department = Only applies to that scope
 * - Priority: Specific assignments > College-level > Global assignments
 */
class WorkflowService
{
    /**
     * Create a new approval workflow
     *
     * @param string $submissionType
     * @param int $submissionId
     * @param User $submitter
     * @return ApprovalWorkflow
     */
    public function createWorkflow(string $submissionType, int $submissionId, User $submitter): ApprovalWorkflow
    {
        $workflow = ApprovalWorkflow::create([
            'submission_type' => $submissionType,
            'submission_id' => $submissionId,
            'current_step' => 1,
            'status' => 'draft',
            'submitted_by' => $submitter->id,
            'college' => $submitter->college->name ?? null,
            'department' => $submitter->department->name ?? null,
        ]);

        return $workflow;
    }

    /**
     * Submit a workflow for approval
     * 
     * Default Workflow: Faculty → Research Coordinator → Dean → Approved
     * Fallback Workflow: Faculty → Dean → Approved (if no coordinator assigned)
     *
     * @param ApprovalWorkflow $workflow
     * @return ApprovalWorkflow
     */
    public function submitWorkflow(ApprovalWorkflow $workflow): ApprovalWorkflow
    {
        DB::transaction(function () use ($workflow) {
            $workflow->status = 'submitted';
            $workflow->current_step = 1;
            
            // STEP 1: Try to find Research Coordinator
            // Priority: Specific assignment > College-level > Global assignment
            $coordinator = $this->findCoordinator($workflow);
            
            if ($coordinator) {
                // DEFAULT WORKFLOW: Coordinator found → Assign to Coordinator (Step 2)
                $workflow->assigned_to = $coordinator->id;
                $workflow->status = 'pending_coordinator';
                $workflow->current_step = 2;
            } else {
                // FALLBACK WORKFLOW: No Coordinator → Skip to Dean (Step 3)
                $dean = $this->findDean($workflow);
                if ($dean) {
                    $workflow->assigned_to = $dean->id;
                    $workflow->status = 'pending_dean';
                    $workflow->current_step = 3;
                    $workflow->fallback_used = true; // Mark that coordinator step was skipped
                } else {
                    // No Dean found either - keep as submitted (will need manual assignment)
                    $workflow->status = 'submitted';
                }
            }
            
            $workflow->save();
            
            // Update submission status to match workflow status
            $submission = $workflow->submission;
            if ($submission) {
                $submission->status = $workflow->status;
                $submission->save();
            }
            
            // Log submission
            $this->logAction($workflow, 'submitted', $workflow->submitter, 'Workflow submitted');
        });

        return $workflow->fresh();
    }

    /**
     * Approve a workflow
     *
     * @param ApprovalWorkflow $workflow
     * @param User $approver
     * @param string|null $comments
     * @return ApprovalWorkflow
     */
    public function approveWorkflow(ApprovalWorkflow $workflow, User $approver, ?string $comments = null): ApprovalWorkflow
    {
        DB::transaction(function () use ($workflow, $approver, $comments) {
            $previousStatus = $workflow->status;
            $submission = $workflow->submission;
            
            if ($workflow->current_step == 2) {
                // Coordinator approved, move to dean
                // Update publication status and store coordinator as approver (intermediate)
                if ($submission) {
                    $submission->status = 'pending_dean';
                    $submission->save();
                }
                
                $dean = $this->findDean($workflow);
                if ($dean) {
                    $workflow->assigned_to = $dean->id;
                    $workflow->status = 'pending_dean';
                    $workflow->current_step = 3;
                } else {
                    // No dean found, auto-approve
                    $workflow->status = 'approved';
                    $workflow->assigned_to = null;
                    // Final approval - store approver_id
                    if ($submission) {
                        $submission->status = 'approved';
                        $submission->approved_at = now();
                        $submission->approver_id = $approver->id; // Store the approver (coordinator in this case)
                        $submission->save();
                    }
                }
            } elseif ($workflow->current_step == 3) {
                // Dean approved, workflow complete
                $workflow->status = 'approved';
                $workflow->assigned_to = null;
                
                // Final approval - store approver_id (Dean)
                if ($submission) {
                    $submission->status = 'approved';
                    $submission->approved_at = now();
                    $submission->approver_id = $approver->id; // Store the Dean as final approver
                    $submission->save();
                }
            }
            
            $workflow->save();
            
            // Log approval
            $this->logAction($workflow, 'approved', $approver, $comments, $previousStatus, $workflow->status);
            
            // If fully approved, finalize (lock points, etc.)
            if ($workflow->status === 'approved') {
                $this->finalizeApproval($workflow, $approver);
            }
        });

        return $workflow->fresh();
    }

    /**
     * Reject a workflow
     *
     * @param ApprovalWorkflow $workflow
     * @param User $rejector
     * @param string $comments
     * @return ApprovalWorkflow
     */
    public function rejectWorkflow(ApprovalWorkflow $workflow, User $rejector, string $comments): ApprovalWorkflow
    {
        DB::transaction(function () use ($workflow, $rejector, $comments) {
            $previousStatus = $workflow->status;
            $workflow->status = 'rejected';
            $workflow->assigned_to = null;
            $workflow->save();
            
            $this->logAction($workflow, 'rejected', $rejector, $comments, $previousStatus, 'rejected');
        });

        return $workflow->fresh();
    }

    /**
     * Return a workflow for revision
     *
     * @param ApprovalWorkflow $workflow
     * @param User $returner
     * @param string $comments
     * @return ApprovalWorkflow
     */
    public function returnWorkflow(ApprovalWorkflow $workflow, User $returner, string $comments): ApprovalWorkflow
    {
        DB::transaction(function () use ($workflow, $returner, $comments) {
            $previousStatus = $workflow->status;
            $workflow->status = 'returned';
            $workflow->current_step = 1;
            $workflow->assigned_to = $workflow->submitted_by;
            $workflow->save();
            
            $this->logAction($workflow, 'returned', $returner, $comments, $previousStatus, 'returned');
        });

        return $workflow->fresh();
    }

    /**
     * Find coordinator for a workflow
     * 
     * Workflow Assignment Logic:
     * - NULL college/department = Global scope (applies to all)
     * - Specific college/department = Only applies to that scope
     * - Priority: Specific assignments > Global assignments
     *
     * @param ApprovalWorkflow $workflow
     * @return User|null
     */
    private function findCoordinator(ApprovalWorkflow $workflow): ?User
    {
        // First try to find specific assignment (college + department match)
        $specificAssignment = WorkflowAssignment::active()
            ->forRole('research_coordinator')
            ->where('college', $workflow->college)
            ->where('department', $workflow->department)
            ->first();
        
        if ($specificAssignment) {
            return $specificAssignment->user;
        }
        
        // Then try college-level assignment (department is NULL = all departments in college)
        $collegeAssignment = WorkflowAssignment::active()
            ->forRole('research_coordinator')
            ->where('college', $workflow->college)
            ->whereNull('department')
            ->first();
        
        if ($collegeAssignment) {
            return $collegeAssignment->user;
        }
        
        // Finally try global assignment (both college and department are NULL = all)
        $globalAssignment = WorkflowAssignment::active()
            ->forRole('research_coordinator')
            ->whereNull('college')
            ->whereNull('department')
            ->first();

        return $globalAssignment ? $globalAssignment->user : null;
    }

    /**
     * Find dean for a workflow
     * 
     * Workflow Assignment Logic:
     * - NULL college = Global scope (applies to all colleges)
     * - Specific college = Only applies to that college
     * - Priority: Specific assignments > Global assignments
     *
     * @param ApprovalWorkflow $workflow
     * @return User|null
     */
    private function findDean(ApprovalWorkflow $workflow): ?User
    {
        // First try to find college-specific assignment
        $collegeAssignment = WorkflowAssignment::active()
            ->forRole('dean')
            ->where('college', $workflow->college)
            ->first();
        
        if ($collegeAssignment) {
            return $collegeAssignment->user;
        }
        
        // Then try global assignment (college is NULL = all colleges)
        $globalAssignment = WorkflowAssignment::active()
            ->forRole('dean')
            ->whereNull('college')
            ->first();

        return $globalAssignment ? $globalAssignment->user : null;
    }
    
    /**
     * Manually reassign a workflow to a specific user (Admin only)
     * 
     * This allows admins to reassign workflows when approvers are unavailable
     *
     * @param ApprovalWorkflow $workflow
     * @param User $newAssignee
     * @param User $reassigner The admin performing the reassignment
     * @param string|null $reason Reason for reassignment
     * @return ApprovalWorkflow
     */
    public function reassignWorkflow(ApprovalWorkflow $workflow, User $newAssignee, User $reassigner, ?string $reason = null): ApprovalWorkflow
    {
        DB::transaction(function () use ($workflow, $newAssignee, $reassigner, $reason) {
            $previousAssignee = $workflow->assignee;
            $workflow->assigned_to = $newAssignee->id;
            $workflow->save();
            
            // Log reassignment
            $comments = $reason ?? 'Workflow manually reassigned by admin';
            if ($previousAssignee) {
                $comments .= sprintf(' (from %s to %s)', $previousAssignee->name, $newAssignee->name);
            } else {
                $comments .= sprintf(' (assigned to %s)', $newAssignee->name);
            }
            
            $this->logAction($workflow, 'reassigned', $reassigner, $comments);
        });

        return $workflow->fresh();
    }

    /**
     * Log an action in approval history
     *
     * @param ApprovalWorkflow $workflow
     * @param string $action
     * @param User $performer
     * @param string|null $comments
     * @param string|null $previousStatus
     * @param string|null $newStatus
     * @return void
     */
    private function logAction(
        ApprovalWorkflow $workflow,
        string $action,
        User $performer,
        ?string $comments = null,
        ?string $previousStatus = null,
        ?string $newStatus = null
    ): void {
        ApprovalHistory::create([
            'workflow_id' => $workflow->id,
            'action' => $action,
            'performed_by' => $performer->id,
            'comments' => $comments,
            'previous_status' => $previousStatus ?? $workflow->getOriginal('status'),
            'new_status' => $newStatus ?? $workflow->status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Finalize approval - update the submission model
     *
     * @param ApprovalWorkflow $workflow
     * @param User $approver The user who approved (final approver)
     * @return void
     */
    private function finalizeApproval(ApprovalWorkflow $workflow, User $approver): void
    {
        $submission = $workflow->submission;
        
        if ($submission) {
            // Status and approver_id should already be set in approveWorkflow
            // But ensure they're set correctly
            if ($submission->status !== 'approved') {
                $submission->status = 'approved';
            }
            if (!$submission->approved_at) {
                $submission->approved_at = now();
            }
            if (!$submission->approver_id) {
                $submission->approver_id = $approver->id;
            }
            
            // Lock points if applicable
            if (property_exists($submission, 'points_locked')) {
                $submission->points_locked = true;
            }
            
            $submission->save();
        }
    }

    /**
     * Auto-escalate workflows that have been pending too long
     *
     * @param int $daysThreshold
     * @return int Number of escalated workflows
     */
    public function autoEscalate(int $daysThreshold = 7): int
    {
        $escalated = 0;
        
        $workflows = ApprovalWorkflow::pending()
            ->where('updated_at', '<', now()->subDays($daysThreshold))
            ->get();

        foreach ($workflows as $workflow) {
            if ($workflow->current_step == 2 && !$workflow->auto_escalated) {
                // Escalate from coordinator to dean
                $dean = $this->findDean($workflow);
                if ($dean) {
                    $workflow->assigned_to = $dean->id;
                    $workflow->status = 'pending_dean';
                    $workflow->current_step = 3;
                    $workflow->auto_escalated = true;
                    $workflow->escalation_date = now();
                    $workflow->save();
                    
                    $this->logAction($workflow, 'reassigned', $workflow->assignee, 'Auto-escalated due to timeout');
                    $escalated++;
                }
            }
        }

        return $escalated;
    }
}

