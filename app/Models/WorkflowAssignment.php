<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowAssignment extends Model
{
    use HasFactory;

    protected $table = 'workflow_assignments';

    protected $fillable = [
        'user_id',
        'role',
        'college',
        'department',
        'is_active',
        'assigned_by',
        'assigned_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the user assigned to this role
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who made this assignment
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope to get active assignments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}

