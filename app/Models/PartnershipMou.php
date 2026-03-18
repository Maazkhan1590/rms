<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnershipMou extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'partnerships_mous';

    protected $fillable = [
        'partner_organization',
        'type',
        'date_signed',
        'expiry_date',
        'scope_theme',
        'lead_staff_id',
        'lead_staff',
        'outputs_papers_grants_events',
        'status',
        'evidence_link',
        'sdg_s',
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
        'date_signed' => 'date',
        'expiry_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
        'year' => 'integer',
    ];

    /**
     * Get the user who submitted this MOU
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved this MOU
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get the lead staff member
     */
    public function leadStaff()
    {
        return $this->belongsTo(User::class, 'lead_staff_id');
    }

    /**
     * Get the policy version used for scoring
     */
    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class);
    }

    /**
     * Get all evidence files for this MOU
     */
    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class, 'submission_id', 'id')
            ->where('submission_type', 'mou');
    }

    /**
     * Get approval workflow for this MOU
     */
    public function workflow()
    {
        return $this->hasOne(ApprovalWorkflow::class, 'submission_id', 'id')
            ->where('submission_type', 'mou');
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
