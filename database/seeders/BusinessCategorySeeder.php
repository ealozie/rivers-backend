<?php

namespace Database\Seeders;

use App\Models\BusinessCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //list business categories in marketing
        $business_category = ['Importer', 'Exporter', 'Manufacturer', 'Wholesaler', 'Retailer', 'Service Provider', 'Others'];
        BusinessCategory::truncate();
        foreach ($business_category as $category) {
            BusinessCategory::create(['name' => $category, 'added_by' => 1, 'status' => 'active']);
        }
    }
}
