<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sedan',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'SUV',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'MPV',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Hatchback',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Pickup',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Van',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Truck',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Coupe',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Convertible',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Wagon',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Crossover',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Minivan',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Sports Car',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Luxury Car',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Hybrid/Electric',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Crossover',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Minivan',
                'status' => 'active',
                'added_by' => 1,
            ],
        ];
        \App\Models\VehicleCategory::truncate();
        foreach ($categories as $category) {
            \App\Models\VehicleCategory::create($category);
        }
    }
}
