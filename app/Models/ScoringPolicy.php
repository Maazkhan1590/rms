<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoringPolicy extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'type',
        'category',
        'subcategory',
        'points',
        'cap',
        'is_active',
        'effective_from',
        'effective_to',
        'version',
        'policy_version_id',
        'conditions',
        'created_by',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'cap' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'conditions' => 'array',
    ];

    /**
     * Get the user who created this policy
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the policy version this policy belongs to
     */
    public function policyVersion()
    {
        return $this->belongsTo(PolicyVersion::class, 'policy_version_id');
    }

    /**
     * Get all scoring rules for this policy
     */
    public function rules()
    {
        return $this->hasMany(ScoringRule::class, 'policy_id');
    }

    /**
     * Scope to get active policies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by effective date
     */
    public function scopeEffectiveOn($query, $date = null)
    {
        $date = $date ?? now();
        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $date);
            });
    }
}

