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
            'individual',
            'cooperate'
        ];


        foreach ($user_roles as $role) {
            $user_role = \Spatie\Permission\Models\Role::where('name', $role)->first();
            if (!$user_role) {
                \Spatie\Permission\Models\Role::create(['name' => $role]);
            }
        }
    }
}
