<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisionExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_name',
        'academic_year',
        'role',
        'degree',
        'university',
        'student_name',
        'thesis_title',
        'start_year',
        'end_year',
        'status',
        'workflow_status',
        'evidence_link',
        'notes',
        'submitted_by',
        'approver_id',
        'submitted_at',
        'approved_at',
        'points_allocated',
        'points_locked',
        'policy_version_id',
        'evidence_required',
        'evidence_uploaded',
        'evidence_description',
    ];

    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
    ];

    /**
     * Get the user that owns the supervision/exam record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class);
    }

    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class, 'submission_id', 'id')
            ->where('submission_type', 'supervision_exam');
    }

    public function workflow()
    {
        return $this->hasOne(ApprovalWorkflow::class, 'submission_id', 'id')
            ->where('submission_type', 'supervision_exam');
    }

    /**
     * Check if the supervision is currently ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->status === 'Ongoing';
    }

    /**
     * Check if the supervision is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }
}
