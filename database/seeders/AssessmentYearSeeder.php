<?php

namespace Database\Seeders;

use App\Models\AssessmentYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssessmentYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessment_years = ['2020', '2021', '2022', '2023', '2024', '2025'];
        AssessmentYear::truncate();
        foreach ($assessment_years as $assessment_year) {
            AssessmentYear::create(['year' => $assessment_year, 'added_by' => 1]);
        }
    }
}
