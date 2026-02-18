<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'awards';

    protected $fillable = [
        'award_name',
        'description',
        'submitted_by',
        'approver_id',
        'awarding_organization',
        'award_date',
        'award_type',
        'category',
        'achievement_description',
        'evidence_link',
        'evidence_description',
        'status',
        'submitted_at',
        'approved_at',
        'points_allocated',
        'points_locked',
        'policy_version_id',
        'evidence_required',
        'evidence_uploaded',
        'year',
    ];

    protected $casts = [
        'award_date' => 'date',
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

    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class);
    }

    public function evidenceFiles()
    {
        return $this->morphMany(EvidenceFile::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'award');
    }

    public function workflow()
    {
        return $this->morphOne(ApprovalWorkflow::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'award');
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
