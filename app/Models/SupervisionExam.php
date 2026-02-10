<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisionExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_name',
        'academic_year',
        'role',
        'degree',
        'university',
        'student_name',
        'thesis_title',
        'start_year',
        'end_year',
        'status',
        'evidence_link',
        'notes',
    ];

    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
    ];

    /**
     * Get the user that owns the supervision/exam record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the supervision is currently ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->status === 'Ongoing';
    }

    /**
     * Check if the supervision is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }
}
