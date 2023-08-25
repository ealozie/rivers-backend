<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Nathaniel', 'email' => 'david.nathaniel13@gmail.com', 'password' => bcrypt('xbba063nath'), 'role' => 'admin'],
        ];
        foreach ($users as $user) {
            $user = \App\Models\User::create($user);
            $user->assignRole('admin');
    }
}
