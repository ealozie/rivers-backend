<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['Flats', 'Rooms'];
        PropertyType::truncate();
        foreach ($types as $type) {
            PropertyType::create(['name' => $type]);
        }
    }
}
