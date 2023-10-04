<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TollGateCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toll_gate_categories = ['Document', 'Payment'];
        \App\Models\TollGateCategory::truncate();
        foreach ($toll_gate_categories as $toll_gate_category) {
            \App\Models\TollGateCategory::create([
                'name' => $toll_gate_category,
            ]);
        }
    }
}
