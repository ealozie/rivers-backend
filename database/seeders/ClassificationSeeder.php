<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classifications = ['Small Shop', 'Medium Shop', 'Big Shop', 'Small Warehouse', 'Medium Warehouse', 'Big Warehouse'];
        Classification::truncate();
        foreach ($classifications as $classification) {
            Classification::create(['name' => $classification]);
        }
    }
}
