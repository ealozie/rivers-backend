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
        // $user_roles = [
        //     'admin',
        //     'agent',
        //     'owner',
        //     'individual',
        //     'cooperate',
        //     'super_agent',
        // ];
        $user_roles = [
            'super_admin',
            'biller',
            'buller_approver',
            'mda_biller',
            'mda_biller_approver',
            'guest_reports',
            'ticket_admin',
            'data_entry',
            'account_officer',
        ];


        foreach ($user_roles as $role) {
            $user_role = \Spatie\Permission\Models\Role::where('name', $role)->first();
            if (!$user_role) {
                \Spatie\Permission\Models\Role::create(['name' => $role]);
            }
        }
    }
}
