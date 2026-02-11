<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class FixSiteSettingsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, ensure permissions exist
        $permissionsToCreate = [
            'slider_access',
            'slider_create',
            'slider_read',
            'slider_update',
            'slider_delete',
            'site_content_access',
            'site_content_create',
            'site_content_read',
            'site_content_update',
            'site_content_delete',
        ];

        $permissionIds = [];
        foreach ($permissionsToCreate as $permissionTitle) {
            $permission = Permission::firstOrCreate(['title' => $permissionTitle]);
            $permissionIds[] = $permission->id;
            $this->command->info("Permission '{$permissionTitle}' ensured (ID: {$permission->id})");
        }

        // Get all permissions for Admin role
        $allPermissions = Permission::all()->pluck('id');

        // Assign all permissions to Admin role
        $adminRole = Role::where('title', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync($allPermissions);
            $this->command->info("Admin role synced with all {$allPermissions->count()} permissions");
        } else {
            $this->command->error("Admin role not found!");
        }

        // Assign to Dean role
        $deanRole = Role::where('title', 'Dean')->first();
        if ($deanRole) {
            $deanPermissions = $deanRole->permissions()->pluck('id');
            $newPermissions = collect($permissionIds)->merge($deanPermissions)->unique();
            $deanRole->permissions()->sync($newPermissions);
            $this->command->info("Dean role synced with site settings permissions");
        }

        $this->command->info("\n✅ Site Settings permissions have been fixed!");
        $this->command->info("Please clear your cache: php artisan cache:clear");
    }
}
