<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RtnSubmission extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'rtn_submissions';

    protected $fillable = [
        'user_id',
        'rtn_type',
        'title',
        'description',
        'student_coauthors',
        'course_files_updated',
        'lecture_materials',
        'assessment_redesign',
        'case_study_documentation',
        'evidence_files',
        'points',
        'year',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'student_coauthors' => 'array',
        'course_files_updated' => 'array',
        'evidence_files' => 'array',
        'points' => 'decimal:2',
        'year' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who submitted this RTN
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by RTN type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('rtn_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

