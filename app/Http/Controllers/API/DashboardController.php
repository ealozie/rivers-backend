<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use Illuminate\Http\Request;

/**
 * @tags Dashboard Service
 */
class DashboardController extends Controller
{

    /**
     * Display aggregation for entities.
     */
    public function metric_for_entities()
    {
        $shops = Shop::count();
        $individuals = Individual::count();
        $cooperates = Cooperate::count();
        $vehicles = CommercialVehicle::count();
        $signage = Signage::count();
        $properties = Property::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'shops' => $shops,
                'individuals' => $individuals,
                'cooperates' => $cooperates,
                'vehicles' => $vehicles,
                'signage' => $signage,
                'properties' => $properties
            ],
        ]);
    }
}
