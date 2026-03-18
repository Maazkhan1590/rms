<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commercialization extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'commercializations';

    protected $fillable = [
        'product_service_name',
        'owner_team_id',
        'owner_team',
        'type',
        'stage',
        'launch_date',
        'revenue_omr',
        'ip_patent',
        'client_market',
        'evidence_link',
        'sdg_s',
        'reporting_period',
        'year',
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
    ];

    protected $casts = [
        'launch_date' => 'date',
        'revenue_omr' => 'decimal:2',
        'ip_patent' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
        'year' => 'integer',
    ];

    /**
     * Get the user who submitted this commercialization
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved this commercialization
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get the owner team member
     */
    public function ownerTeam()
    {
        return $this->belongsTo(User::class, 'owner_team_id');
    }

    /**
     * Get the policy version used for scoring
     */
    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class);
    }

    /**
     * Get all evidence files for this commercialization
     */
    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class, 'submission_id', 'id')
            ->where('submission_type', 'commercialization');
    }

    /**
     * Get approval workflow for this commercialization
     */
    public function workflow()
    {
        return $this->hasOne(ApprovalWorkflow::class, 'submission_id', 'id')
            ->where('submission_type', 'commercialization');
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
