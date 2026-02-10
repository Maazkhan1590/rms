<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_name',
        'journal_conference',
        'role',
        'start_date',
        'end_date',
        'evidence_link',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user that owns the editorial appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the appointment is currently active.
     */
    public function isActive(): bool
    {
        $now = now();
        $startPassed = !$this->start_date || $this->start_date <= $now;
        $notEnded = !$this->end_date || $this->end_date >= $now;
        
        return $startPassed && $notEnded;
    }
}
