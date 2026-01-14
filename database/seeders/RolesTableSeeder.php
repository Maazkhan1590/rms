<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id'    => 1,
                'title' => 'Admin',
            ],
            [
                'id'    => 2,
                'title' => 'Dean',
            ],
            [
                'id'    => 3,
                'title' => 'Coordinator',
            ],
            [
                'id'    => 4,
                'title' => 'Faculty',
            ],
            [
                'id'    => 5,
                'title' => 'Student',
            ],
        ];

        Role::insert($roles);
    }
}
