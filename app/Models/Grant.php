<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grant extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'grants';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'amount',
        'currency',
        'status',
        'sponsor',
        'reference_code',
        'start_date',
        'end_date',
        'submitted_by',
        'approver_id',
        'submitted_at',
        'approved_at',
        'grant_type',
        'role',
        'amount_omr',
        'units',
        'sponsor_type',
        'sponsor_name',
        'matching_grant_moa',
        'patent_registration_number',
        'patent_su_registered',
        'award_letter_path',
        'points_allocated',
        'policy_version_id',
        'points_locked',
        'evidence_required',
        'evidence_uploaded',
        'award_year',
        'submission_year',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
        'start_date'   => 'date',
        'end_date'     => 'date',
        'amount' => 'decimal:2',
        'amount_omr' => 'decimal:2',
        'units' => 'integer',
        'patent_su_registered' => 'boolean',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
        'award_year' => 'integer',
        'submission_year' => 'integer',
    ];

    /**
     * Get the user who submitted this grant
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved this grant
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get the policy version used for scoring
     */
    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class);
    }

    /**
     * Get all evidence files for this grant
     */
    public function evidenceFiles()
    {
        return $this->morphMany(EvidenceFile::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'grant');
    }

    /**
     * Get approval workflow for this grant
     */
    public function workflow()
    {
        return $this->morphOne(ApprovalWorkflow::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'grant');
    }

    /**
     * Calculate units based on amount_omr
     */
    public function calculateUnits()
    {
        if ($this->amount_omr) {
            $this->units = (int) ceil($this->amount_omr / 10000);
        }
        return $this;
    }

    /**
     * Scope to filter by grant type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('grant_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by role
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
