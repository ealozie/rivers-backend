<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BloodGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        \App\Models\BloodGroup::truncate();
        foreach ($blood_groups as $blood_group) {
            \App\Models\BloodGroup::create(['name' => $blood_group]);
        }
    }
}
