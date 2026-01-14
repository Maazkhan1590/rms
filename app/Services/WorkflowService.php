<?php

namespace App\Services;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalHistory;
use App\Models\WorkflowAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
     * @param ApprovalWorkflow $workflow
     * @return ApprovalWorkflow
     */
    public function submitWorkflow(ApprovalWorkflow $workflow): ApprovalWorkflow
    {
        DB::transaction(function () use ($workflow) {
            $workflow->status = 'submitted';
            $workflow->current_step = 1;
            
            // Find coordinator
            $coordinator = $this->findCoordinator($workflow);
            
            if ($coordinator) {
                $workflow->assigned_to = $coordinator->id;
                $workflow->status = 'pending_coordinator';
                $workflow->current_step = 2;
            } else {
                // No coordinator found, skip to dean
                $dean = $this->findDean($workflow);
                if ($dean) {
                    $workflow->assigned_to = $dean->id;
                    $workflow->status = 'pending_dean';
                    $workflow->current_step = 3;
                    $workflow->fallback_used = true;
                }
            }
            
            $workflow->save();
            
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
            
            if ($workflow->current_step == 2) {
                // Coordinator approved, move to dean
                $dean = $this->findDean($workflow);
                if ($dean) {
                    $workflow->assigned_to = $dean->id;
                    $workflow->status = 'pending_dean';
                    $workflow->current_step = 3;
                } else {
                    // No dean found, auto-approve
                    $workflow->status = 'approved';
                    $workflow->assigned_to = null;
                }
            } elseif ($workflow->current_step == 3) {
                // Dean approved, workflow complete
                $workflow->status = 'approved';
                $workflow->assigned_to = null;
            }
            
            $workflow->save();
            
            // Log approval
            $this->logAction($workflow, 'approved', $approver, $comments, $previousStatus, $workflow->status);
            
            // If fully approved, update the submission
            if ($workflow->status === 'approved') {
                $this->finalizeApproval($workflow);
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
     * @param ApprovalWorkflow $workflow
     * @return User|null
     */
    private function findCoordinator(ApprovalWorkflow $workflow): ?User
    {
        $assignment = WorkflowAssignment::active()
            ->forRole('research_coordinator')
            ->where(function ($query) use ($workflow) {
                $query->whereNull('college')
                    ->orWhere('college', $workflow->college);
            })
            ->where(function ($query) use ($workflow) {
                $query->whereNull('department')
                    ->orWhere('department', $workflow->department);
            })
            ->first();

        return $assignment ? $assignment->user : null;
    }

    /**
     * Find dean for a workflow
     *
     * @param ApprovalWorkflow $workflow
     * @return User|null
     */
    private function findDean(ApprovalWorkflow $workflow): ?User
    {
        $assignment = WorkflowAssignment::active()
            ->forRole('dean')
            ->where(function ($query) use ($workflow) {
                $query->whereNull('college')
                    ->orWhere('college', $workflow->college);
            })
            ->first();

        return $assignment ? $assignment->user : null;
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
     * @return void
     */
    private function finalizeApproval(ApprovalWorkflow $workflow): void
    {
        $submission = $workflow->submission;
        
        if ($submission) {
            $submission->status = 'approved';
            $submission->approved_at = now();
            $submission->approver_id = $workflow->assignee->id ?? null;
            $submission->save();
            
            // Lock points if applicable
            if (method_exists($submission, 'update')) {
                $submission->points_locked = true;
                $submission->save();
            }
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

