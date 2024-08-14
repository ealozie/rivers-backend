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
     * Display aggregation for Shops.
     */
    public function shops_aggregates()
    {
        $shops = Shop::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'shops' => $shops
            ],
        ]);
    }

    /**
     * Display aggregation for individuals.
     */
    public function individuals_aggregates()
    {
        $individuals = Individual::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'individuals' => $individuals,
            ],
        ]);
    }

     /**
     * Display aggregation for cooperates.
     */
    public function cooperates_aggregates()
    {
        $cooperates = Cooperate::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'cooperates' => $cooperates,
            ],
        ]);
    }

    /**
     * Display aggregation for signage.
     */
    public function signage_aggregates()
    {
        $signage = Signage::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'signage' => $signage,
            ],
        ]);
    }

    /**
     * Display aggregation for properties.
     */
    public function properties_aggregates()
    {
        $properties = Property::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'properties' => $properties
            ],
        ]);
    }

    /**
     * Display aggregation for vehicles.
     */
    public function vehicles_aggregates()
    {
        $vehicles = CommercialVehicle::count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'vehicles' => $vehicles,
            ],
        ]);
    }
}
