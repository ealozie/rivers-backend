<?php

namespace Database\Seeders;

use App\Models\PropertyUse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uses = ['Residential', 'Commercial', 'Industrial', 'Government'];
        PropertyUse::truncate();
        foreach ($uses as $use) {
            PropertyUse::create(['name' => $use]);
        }
    }
}
