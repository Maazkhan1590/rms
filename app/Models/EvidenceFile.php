<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvidenceFile extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'submission_type',
        'submission_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'file_category',
        'uploaded_by',
        'uploaded_at',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user who uploaded this file
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the user who verified this file
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the related submission (polymorphic)
     */
    public function submission()
    {
        return $this->morphTo('submission', 'submission_type', 'submission_id');
    }

    /**
     * Scope to get verified files
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('file_category', $category);
    }
}

