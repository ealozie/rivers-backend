<?php

namespace Database\Seeders;

use App\Models\PropertyCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $property_categories = ['Open Lands', 'Building'];
        PropertyCategory::truncate();
        foreach ($property_categories as $property_category) {
            PropertyCategory::create(['name' => $property_category]);
        }
    }
}
