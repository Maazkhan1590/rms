<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_name',
        'journal_conference',
        'role',
        'start_date',
        'end_date',
        'evidence_link',
        'notes',
        'status',
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
        'year',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
        'year' => 'integer',
    ];

    /**
     * Get the user that owns the editorial appointment.
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
            ->where('submission_type', 'editorial_appointment');
    }

    public function workflow()
    {
        return $this->hasOne(ApprovalWorkflow::class, 'submission_id', 'id')
            ->where('submission_type', 'editorial_appointment');
    }

    /**
     * Check if the appointment is currently active.
     */
    public function isActive(): bool
    {
        $now = now();
        $startPassed = !$this->start_date || $this->start_date <= $now;
        $notEnded = !$this->end_date || $this->end_date >= $now;
        
        return $startPassed && $notEnded;
    }
}
