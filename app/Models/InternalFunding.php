<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalFunding extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'internal_fundings';

    protected $fillable = [
        'user_id',
        'project_title',
        'funding_source',
        'amount_omr',
        'start_date',
        'end_date',
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
        'evidence_link',
        'notes',
        'year',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_omr' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
        'year' => 'integer',
    ];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class);
    }

    public function evidenceFiles()
    {
        return $this->morphMany(EvidenceFile::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'internal_funding');
    }

    public function workflow()
    {
        return $this->morphOne(ApprovalWorkflow::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'internal_funding');
    }
}
