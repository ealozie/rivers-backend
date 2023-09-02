<?php

namespace Database\Seeders;

use App\Models\SettlementType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettlementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settlement_types = ['Urban', 'Rural'];
        SettlementType::truncate();
        foreach ($settlement_types as $settlement_type) {
            SettlementType::create(['name' => $settlement_type]);
        }
    }
}
