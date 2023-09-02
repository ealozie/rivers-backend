<?php

namespace Database\Seeders;

use App\Models\BusinessLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $business_levels = ['High', 'Medium', 'Low'];
        BusinessLevel::truncate();
        foreach ($business_levels as $business_level) {
            BusinessLevel::create(['name' => $business_level]);
        }
    }
}
