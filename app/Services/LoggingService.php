<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LoggingService
{
    /**
     * Log an audit event (model changes)
     */
    public function logAudit(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): AuditLog {
        $changes = null;
        if ($oldValues && $newValues) {
            $changes = $this->calculateChanges($oldValues, $newValues);
        }

        return AuditLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log an activity event (user actions)
     */
    public function logActivity(
        string $activityType,
        string $description,
        ?string $relatedModelType = null,
        ?int $relatedModelId = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'activity_type' => $activityType,
            'description' => $description,
            'related_model_type' => $relatedModelType,
            'related_model_id' => $relatedModelId,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Calculate changes between old and new values
     */
    protected function calculateChanges(array $oldValues, array $newValues): array
    {
        $changes = [];
        foreach ($newValues as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }
        return $changes;
    }
}
