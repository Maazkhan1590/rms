<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoringRule extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'policy_id',
        'rule_name',
        'rule_type',
        'points',
        'conditions',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'conditions' => 'array',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the policy this rule belongs to
     */
    public function policy()
    {
        return $this->belongsTo(ScoringPolicy::class, 'policy_id');
    }

    /**
     * Scope to get active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by priority
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }
}

