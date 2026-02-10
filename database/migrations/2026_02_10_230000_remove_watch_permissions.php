<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove watch-related permissions from roles and permissions tables
        $watchPermissionIds = DB::table('permissions')
            ->whereIn('title', [
                'watch_create',
                'watch_edit',
                'watch_show',
                'watch_delete',
                'watch_access',
            ])
            ->pluck('id')
            ->all();

        if (!empty($watchPermissionIds)) {
            DB::table('permission_role')
                ->whereIn('permission_id', $watchPermissionIds)
                ->delete();

            DB::table('permissions')
                ->whereIn('id', $watchPermissionIds)
                ->delete();
        }
    }

    public function down(): void
    {
        // Intentionally left empty – watch permissions are not needed anymore
    }
};

