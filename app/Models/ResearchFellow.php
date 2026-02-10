<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchFellow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'staff_name',
        'publication_title',
            'journal',
            'doi',
            'status',
        'indexed',
        'year',
        'count_for_urc',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'count_for_urc' => 'integer',
    ];

    /**
     * Get the user that owns the research fellowship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
