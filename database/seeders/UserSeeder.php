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
        // $users = [
        //     ['name' => 'Nathaniel', 'email' => 'david.nathaniel13@gmail.com', 'password' => bcrypt('xbba063nath'), 'role' => 'admin'],
        // ];
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $names = explode('@', $user->email);
            $other_names = explode('.', $names);
            if (count($other_names) > 1) {
                $user->name = ucfirst($other_names[0]);
            } else {
                $user->name = ucfirst($names[0]);
            }
            $user->save();
            
        }
    }
}
