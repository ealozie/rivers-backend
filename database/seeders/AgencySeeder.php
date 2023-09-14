<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies_and_code = ['20008001' => 'Board of Internal Revenue', '26051001' => 'Judiciary - High Court', '54001001' => 'Min of Rural Development, Cooperatives & Poverty Reduction', '15001001' => 'Ministry of Agriculture', '21001001' => 'Ministry of Lands and Survey', '22001001' => 'Ministry of Trade and Commerce', '29001001' => 'Ministry of Transport'];
        \App\Models\Agency::truncate();
        foreach ($agencies_and_code as $code => $agency) {
            \App\Models\Agency::create(['agency_code' => $code, 'agency_name' => $agency, 'status' => 'active', 'added_by' => 1]);
        }
    }
}
