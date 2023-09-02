<?php

namespace Database\Seeders;

use App\Models\GenoType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $geno_types = ['AA', 'AS', 'SS'];
        GenoType::truncate();
        foreach ($geno_types as $geno_type) {
            GenoType::create(['name' => $geno_type]);
        }
    }
}
