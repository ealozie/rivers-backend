<?php

namespace Database\Seeders;

use App\Models\BusinessSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //list the business categories id and sub categories
        $business_sub_categories = [['business_category_id' => 1, 'name' => 'Food Items'], ['business_category_id' => 1, 'name' => 'Electronics'], ['business_category_id' => 1, 'name' => 'Clothing'], ['business_category_id' => 1, 'name' => 'Others'], ['business_category_id' => 2, 'name' => 'Food Items'], ['business_category_id' => 2, 'name' => 'Electronics'], ['business_category_id' => 2, 'name' => 'Clothing'], ['business_category_id' => 2, 'name' => 'Others'], ['business_category_id' => 3, 'name' => 'Food Items'], ['business_category_id' => 3, 'name' => 'Electronics'], ['business_category_id' => 3, 'name' => 'Clothing'], ['business_category_id' => 3, 'name' => 'Others'], ['business_category_id' => 4, 'name' => 'Food Items'], ['business_category_id' => 4, 'name' => 'Electronics'], ['business_category_id' => 4, 'name' => 'Clothing'], ['business_category_id' => 4, 'name' => 'Others'], ['business_category_id' => 5, 'name' => 'Food Items'], ['business_category_id' => 5, 'name' => 'Electronics'], ['business_category_id' => 5, 'name' => 'Clothing'], ['business_category_id' => 5, 'name' => 'Others'], ['business_category_id' => 6, 'name' => 'Food Items'], ['business_category_id' => 6, 'name' => 'Electronics'], ['business_category_id' => 6, 'name' => 'Clothing'], ['business_category_id' => 6, 'name' => 'Others'], ['business_category_id' => 7, 'name' => 'Food Items'], ['business_category_id' => 7, 'name' => 'Electronics'], ['business_category_id' => 7, 'name' => 'Clothing'], ['business_category_id' => 7, 'name' => 'Others']];
        BusinessSubCategory::truncate();
        foreach ($business_sub_categories as $business_sub_category) {
            $business_sub_category['added_by'] = 1;
            BusinessSubCategory::create($business_sub_category);
        }
    }
}
