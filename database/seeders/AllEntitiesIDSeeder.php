<?php

namespace Database\Seeders;

use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllEntitiesIDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::all();
        $signages = Signage::all();
        $vehicles = CommercialVehicle::all();
        $shops = Shop::all();
        $individuals = Individual::all();
        $cooperates = Cooperate::all();
        if (count($properties)) {
            foreach ($properties as $property) {
                $property->property_id = '4'.date('hi').mt_rand(11111, 99999);
                $property->save();
            }
        }
        if (count($signages)) {
            foreach ($signages as $property) {
                $property->signage_id = '5'.date('hi').mt_rand(11111, 99999);
                $property->save();
            }
        }
        if (count($vehicles)) {
            foreach ($vehicles as $property) {
                $property->vehicle_id = '6'.date('hi').mt_rand(11111, 99999);
                $property->save();
            }
        }
        if (count($shops)) {
            foreach ($shops as $property) {
                $property->shop_id = '3'.date('hi').mt_rand(11111, 99999);
                $property->save();
            }
        }
        if (count($individuals)) {
            foreach ($individuals as $property) {
                $property->individual_id = '1'.date('hi').mt_rand(11111, 99999);
                $property->save();
            }
        }
        if (count($cooperates)) {
            foreach ($cooperates as $property) {
                $property->cooperate_id = '2'.date('hi').mt_rand(11111, 99999);
                $property->save();
            }
        }
    }
}
