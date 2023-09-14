<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserUniqueIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //generate unique id of 10 digits for each user without repetition
        $unique_ids = [];
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $unique_id = random_int(1000000000, 9999999999);
            while (in_array($unique_id, $unique_ids)) {
                $unique_id = random_int(1000000000, 9999999999);
            }
            $unique_ids[] = $unique_id;
            $user->unique_id = $unique_id;
            $user->save();
        }
    }
}
