<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manufacturers = [
            [
                'name' => 'Toyota',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Honda',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Nissan',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Mitsubishi',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Suzuki',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Isuzu',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Mazda',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Subaru',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Daihatsu',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Lexus',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Audi',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'BMW',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Mercedes-Benz',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Volkswagen',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Volvo',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Ford',
                'status' => 'active',
                'added_by' => 1,
            ],
            [
                'name' => 'Chevrolet',
                'status' => 'active',
                'added_by' => 1,
            ],
        ];
        \App\Models\VehicleManufacturer::truncate();
        foreach ($manufacturers as $manufacturer) {
            \App\Models\VehicleManufacturer::create($manufacturer);
        }
    }
}
