<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalHistory extends Model
{
    use HasFactory;

    protected $table = 'approval_history';

    protected $fillable = [
        'workflow_id',
        'action',
        'performed_by',
        'comments',
        'evidence_files',
        'previous_status',
        'new_status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'evidence_files' => 'array',
    ];

    /**
     * Get the workflow this history belongs to
     */
    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'workflow_id');
    }

    /**
     * Get the user who performed this action
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}

