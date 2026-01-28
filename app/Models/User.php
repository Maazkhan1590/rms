<?php

namespace App\Models;

use Hash;
use Carbon\Carbon;
use DateTimeInterface;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, HasFactory,HasApiTokens;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'last_login_at',
        'last_points_calculation',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'total_research_points' => 'decimal:2',
    ];

    protected $fillable = [
        'name',
        'email',
        'status',
        'college_id',
        'department_id',
        'employee_id',
        'designation',
        'phone',
        'profile_photo',
        'orcid',
        'google_scholar',
        'research_gate',
        'credentials_file',
        'total_research_points',
        'last_points_calculation',
        'last_login_at',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the college this user belongs to
     */
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get the department this user belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all publications submitted by this user
     */
    public function publications()
    {
        return $this->hasMany(Publication::class, 'submitted_by');
    }

    /**
     * Get all publications where this user is primary author
     */
    public function primaryAuthorPublications()
    {
        return $this->hasMany(Publication::class, 'primary_author_id');
    }

    /**
     * Get all publications where this user is author (submitted or primary author)
     */
    public function publicationsAsAuthor()
    {
        return Publication::where(function($query) {
            $query->where('submitted_by', $this->id)
                  ->orWhere('primary_author_id', $this->id);
        });
    }

    /**
     * Get all grants submitted by this user
     */
    public function grants()
    {
        return $this->hasMany(Grant::class, 'submitted_by');
    }

    /**
     * Get all RTN submissions by this user
     */
    public function rtnSubmissions()
    {
        return $this->hasMany(RtnSubmission::class);
    }

    /**
     * Get all bonus recognitions for this user
     */
    public function bonusRecognitions()
    {
        return $this->hasMany(BonusRecognition::class);
    }

    /**
     * Get all approval workflows submitted by this user
     */
    public function submittedWorkflows()
    {
        return $this->hasMany(ApprovalWorkflow::class, 'submitted_by');
    }

    /**
     * Get all approval workflows assigned to this user
     */
    public function assignedWorkflows()
    {
        return $this->hasMany(ApprovalWorkflow::class, 'assigned_to');
    }

    /**
     * Get all workflow assignments for this user
     */
    public function workflowAssignments()
    {
        return $this->hasMany(WorkflowAssignment::class);
    }

    /**
     * Get all evidence files uploaded by this user
     */
    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class, 'uploaded_by');
    }

    /**
     * Get all pipeline submissions by this user
     */
    public function pipelineSubmissions()
    {
        return $this->hasMany(PipelineSubmission::class);
    }

    /**
     * Get all audit logs for this user
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get all activity logs for this user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user is a research coordinator
     */
    public function isResearchCoordinator()
    {
        return $this->workflowAssignments()
            ->where('role', 'research_coordinator')
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user is a dean
     */
    public function isDean()
    {
        return $this->workflowAssignments()
            ->where('role', 'dean')
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('title', $role)->exists();
        }
        
        if (is_numeric($role)) {
            return $this->roles()->where('id', $role)->exists();
        }
        
        return false;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles)
    {
        return $this->roles()->whereIn('title', $roles)->exists();
    }
}
