<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusRecognition extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'bonus_recognition';

    protected $fillable = [
        'user_id',
        'recognition_type',
        'title',
        'organization',
        'role_description',
        'journal_conference_name',
        'event_name',
        'event_date',
        'evidence_files',
        'points',
        'year',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'evidence_files' => 'array',
        'points' => 'decimal:2',
        'year' => 'integer',
        'event_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who submitted this recognition
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by recognition type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('recognition_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}

