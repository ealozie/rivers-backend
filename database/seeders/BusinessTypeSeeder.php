<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $business_types = ['BN', 'PLC', 'ENTERPRISE', 'NGO', 'OTHERS'];
        BusinessType::truncate();
        foreach ($business_types as $business_type) {
            BusinessType::create(['name' => $business_type]);
        }
    }
}
