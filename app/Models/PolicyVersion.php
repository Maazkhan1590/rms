<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'version_number',
        'year',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this policy version
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all scoring policies for this version
     */
    public function scoringPolicies()
    {
        return $this->hasMany(ScoringPolicy::class, 'policy_version_id');
    }

    /**
     * Get publications using this policy version
     */
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    /**
     * Get grants using this policy version
     */
    public function grants()
    {
        return $this->hasMany(Grant::class);
    }

    /**
     * Scope to get active policy version
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full name attribute (version number + year)
     */
    public function getFullNameAttribute()
    {
        return $this->version_number . ' (' . $this->year . ')';
    }
}

