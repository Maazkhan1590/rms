<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    public function run()
    {
        $roleMap = Role::whereIn('title', ['Admin', 'Dean', 'Coordinator', 'Faculty'])
            ->pluck('id', 'title');

        $assignments = [
            'Admin'       => 1,
            'Dean'        => 2,
            'Coordinator' => 3,
            'Faculty'     => 4,
        ];

        foreach ($assignments as $roleTitle => $userId) {
            $roleId = $roleMap->get($roleTitle);
            $user   = User::find($userId);

            if ($roleId && $user) {
                $user->roles()->sync([$roleId]);
            }
        }
    }
}
