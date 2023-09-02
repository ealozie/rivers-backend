<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $default_settings = [
            'APP_NAME' => 'Ticketing System',
            'ALLOW_TICKET_VENDING' => true,
            'ALLOW_TICKET_BULK_VENDING' => true,
            'ALLOW_TICKET_ENFORCEMENT' => true,
            'ALLOW_WALLET_FUND_TRANSFER' => true,
            'ALLOW_TICKETING_ON_SATURDAY' => true,
            'ALLOW_TICKETING_ON_SUNDAY' => false,
            'APP_LOGO' => 'https://via.placeholder.com/150',
        ];

        AppSetting::truncate();
        foreach ($default_settings as $key => $value) {
            AppSetting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
