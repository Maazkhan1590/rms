<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipelineSubmission extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'pipeline_submissions';

    protected $fillable = [
        'user_id',
        'submission_type',
        'title',
        'status',
        'journal_conference_name',
        'submission_date',
        'expected_decision_date',
        'notes',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'expected_decision_date' => 'date',
    ];

    /**
     * Get the user who submitted this
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by submission type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('submission_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

