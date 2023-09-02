<?php

namespace Database\Seeders;

use App\Models\MarketName;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $market_names = [
            ['name' => 'Aba Main Market'],
            ['name' => 'Aba New Market'],
            ['name' => 'Aba Spare Parts Market'],
            ['name' => 'Aba Timber Market'],
            ['name' => 'Aba Town Market'],
            ['name' => 'Aba Village Market'],
            ['name' => 'Abakaliki Main Market'],
            ['name' => 'Abakpa Main Market'],
            ['name' => 'Abakpa Market'],
            ['name' => 'Abakpa Nike Market'],
            ['name' => 'Abakpa Spare Parts Market'],
            ['name' => 'Abakpa Timber Market'],
            ['name' => 'Abakpa Village Market'],
            ['name' => 'Abattoir Market'],
            ['name' => 'Abba Market'],
            ['name' => 'Abba Spare Parts Market'],
            ['name' => 'Abba Timber Market'],
            ['name' => 'Abba Village Market'],
            ['name' => 'Abia State University Market'],
            ['name' => 'Abiriba Market'],
            ['name' => 'Abiriba Spare Parts Market'],
            ['name' => 'Abiriba Timber Market'],
            ['name' => 'Abiriba Village Market'],
            ['name' => 'Abriba Market'],
            ['name' => 'Abriba Spare Parts Market'],
            ['name' => 'Abriba Timber Market']
        ];
        MarketName::truncate();
        foreach ($market_names as $market_name) {
            $market_name['added_by'] = 1;
            MarketName::create($market_name);
        }
    }
}
