<?php

namespace Database\Seeders;

use App\Models\DemandNoticeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemandNoticeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demand_notice_categories = ['Demand Notice', 'Demand Notice Reminder', 'Demand Notice Final Reminder', 'Demand Notice Cancellation'];
        DemandNoticeCategory::truncate();
        foreach ($demand_notice_categories as $demand_notice_category) {
            DemandNoticeCategory::create(['name' => $demand_notice_category]);
        }
    }
}
