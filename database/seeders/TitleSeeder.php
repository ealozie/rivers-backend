<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = ['Mr', 'Mrs', 'Miss', 'Dr', 'Prof'];
        Title::truncate();
        foreach ($titles as $title) {
            Title::create(['name' => $title]);
        }
    }
}
