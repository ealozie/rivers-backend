<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_roles = [
            'admin',
            'agent',
            'owner',
        ];
        $roles = \Spatie\Permission\Models\Role::count();
        if (!$roles) {
            foreach ($user_roles as $role) {
                \Spatie\Permission\Models\Role::create(['name' => $role]);
            }
        }
    }
}
