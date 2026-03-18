<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchFellow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_name',
        'publication_title',
        'journal',
        'doi',
        'status',
        'workflow_status',
        'indexed',
        'year',
        'count_for_urc',
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
        'evidence_link',
    ];

    protected $casts = [
        'year' => 'integer',
        'count_for_urc' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
    ];

    /**
     * Get the user that owns the research fellowship.
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
            ->where('submission_type', 'research_fellow');
    }

    public function workflow()
    {
        return $this->hasOne(ApprovalWorkflow::class, 'submission_id', 'id')
            ->where('submission_type', 'research_fellow');
    }
}
