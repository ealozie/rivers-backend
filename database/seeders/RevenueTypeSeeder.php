<?php

namespace Database\Seeders;

use App\Models\RevenueType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RevenueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $revenue_types = ['Fee', 'Fine', 'Taxes', 'Levies'];
        RevenueType::truncate();
        foreach ($revenue_types as $revenue_type) {
            RevenueType::create(['name' => $revenue_type]);
        }
    }
}
