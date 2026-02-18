<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RtnCourseDetail extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'rtn_course_details';

    protected $fillable = [
        'user_id',
        'course_code',
        'course_name',
        'rtn_type',
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
        'year',
        'notes',
    ];

    protected $casts = [
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
            ->where('submission_type', 'rtn_course_detail');
    }

    public function workflow()
    {
        return $this->morphOne(ApprovalWorkflow::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'rtn_course_detail');
    }
}
