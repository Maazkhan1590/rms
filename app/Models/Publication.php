<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'publications';

    protected $fillable = [
        'title',
        'slug',
        'abstract',
        'status',
        'submitted_by',
        'approver_id',
        'submitted_at',
        'approved_at',
        'department',
        'college',
        'doi',
        'journal',
        'publication_year',
        'published_at',
        'publication_type',
        'journal_category',
        'quartile',
        'authors',
        'primary_author_id',
        'co_authors',
        'isbn',
        'publisher',
        'journal_name',
        'conference_name',
        'proceedings_link',
        'published_link',
        'acceptance_letter_path',
        'points_allocated',
        'policy_version_id',
        'points_locked',
        'evidence_required',
        'evidence_uploaded',
        'year',
        'submission_year',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
        'published_at' => 'date',
        'authors' => 'array',
        'co_authors' => 'array',
        'points_allocated' => 'decimal:2',
        'points_locked' => 'boolean',
        'evidence_required' => 'boolean',
        'evidence_uploaded' => 'boolean',
        'year' => 'integer',
        'submission_year' => 'integer',
    ];

    /**
     * Get the user who submitted this publication
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved this publication
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get the primary author
     */
    public function primaryAuthor()
    {
        return $this->belongsTo(User::class, 'primary_author_id');
    }

    /**
     * Get the policy version used for scoring
     */
    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class);
    }

    /**
     * Get all evidence files for this publication
     */
    public function evidenceFiles()
    {
        return $this->morphMany(EvidenceFile::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'publication');
    }

    /**
     * Get approval workflow for this publication
     */
    public function workflow()
    {
        return $this->morphOne(ApprovalWorkflow::class, 'submission', 'submission_type', 'submission_id')
            ->where('submission_type', 'publication');
    }

    /**
     * Scope to filter by publication type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('publication_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by journal category
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('journal_category', $category);
    }
}
