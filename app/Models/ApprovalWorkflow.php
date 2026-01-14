<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalWorkflow extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'submission_type',
        'submission_id',
        'current_step',
        'status',
        'submitted_by',
        'assigned_to',
        'college',
        'department',
        'fallback_used',
        'auto_escalated',
        'escalation_date',
    ];

    protected $casts = [
        'current_step' => 'integer',
        'fallback_used' => 'boolean',
        'auto_escalated' => 'boolean',
        'escalation_date' => 'datetime',
    ];

    /**
     * Get the user who submitted this workflow
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user currently assigned to approve
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all approval history entries
     */
    public function history()
    {
        return $this->hasMany(ApprovalHistory::class, 'workflow_id');
    }

    /**
     * Get the related submission (polymorphic)
     * Note: This is a manual relationship since we're using enum types
     */
    public function getSubmissionAttribute()
    {
        switch ($this->submission_type) {
            case 'publication':
                return Publication::find($this->submission_id);
            case 'grant':
                return Grant::find($this->submission_id);
            case 'rtn':
                return RtnSubmission::find($this->submission_id);
            case 'bonus':
                return BonusRecognition::find($this->submission_id);
            default:
                return null;
        }
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending workflows
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending_coordinator', 'pending_dean', 'submitted']);
    }
}

