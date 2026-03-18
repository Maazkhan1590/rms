<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultancy extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'consultancies_kts';

    protected $fillable = [
        'user_id',
        'project_consultancy_name',
        'start_date',
        'end_date',
        'client_sponsor',
        'amount_omr',
        'status',
        'commercialized',
        'income_type',
        'lead_staff',
        'evidence_link',
        'sdg_s',
        'reporting_period',
        'year',
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
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_omr' => 'decimal:2',
        'commercialized' => 'boolean',
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
        return $this->hasMany(EvidenceFile::class, 'submission_id', 'id')
            ->where('submission_type', 'consultancy');
    }

    public function workflow()
    {
        return $this->hasOne(ApprovalWorkflow::class, 'submission_id', 'id')
            ->where('submission_type', 'consultancy');
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
