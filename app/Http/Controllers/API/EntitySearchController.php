<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommercialVehicleResource;
use App\Http\Resources\CooperateResource;
use App\Http\Resources\IndividualResource;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SignageResource;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use Illuminate\Http\Request;

class EntitySearchController extends Controller
{
    /**
     * Entity search request.
     *
     * Query parameters:
     * - entity_type: individual, cooperate, commercial_vehicle, shop, property, signage
     * - street_id: street id
     * - local_government_area_id: local government area id
     */
    public function __invoke(Request $request)
    {
        if ( ! $request->has('entity_type')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Entity type is required',
            ], 400);
        }
        $entity_type = $request->get('entity_type');
        if ( ! in_array($entity_type, ['individual', 'cooperate', 'commercial_vehicle', 'shop', 'property', 'signage'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid entity type',
            ], 400);
        }

        if ($entity_type === 'individual') {
            $individuals = Individual::query();
            if ($request->has('street_id')) {
                $individuals->where('street_id', $request->get('street_id'));
            }
            if ($request->has('local_government_area_id')) {
                $individuals->where('local_government_area_id', $request->get('local_government_area_id'));
            }
            return IndividualResource::collection($individuals->paginate());
        }
        if ($entity_type === 'cooperate') {
            $cooperates = Cooperate::query();
            if ($request->has('street_id')) {
                $cooperates->where('street_id', $request->get('street_id'));
            }
            if ($request->has('local_government_area_id')) {
                $cooperates->where('local_government_area_id', $request->get('local_government_area_id'));
            }
            return CooperateResource::collection($cooperates->paginate());
        }
        if ($entity_type === 'commercial_vehicle') {
            $commercial_vehicles = CommercialVehicle::query();
            if ($request->has('street_id')) {
                $commercial_vehicles->where('street_id', $request->get('street_id'));
            }
            if ($request->has('local_government_area_id')) {
                $commercial_vehicles->where('local_government_area_id', $request->get('local_government_area_id'));
            }
            return CommercialVehicleResource::collection($commercial_vehicles->paginate());
        }
        if ($entity_type === 'property') {
            $properties = Property::query();
            if ($request->has('street_id')) {
                $properties->where('street_id', $request->get('street_id'));
            }
            if ($request->has('local_government_area_id')) {
                $properties->where('local_government_area_id', $request->get('local_government_area_id'));
            }
            return PropertyResource::collection($properties->paginate());
        }

        if ($entity_type === 'shop') {
            $shops = Shop::query();
            if ($request->has('street_id')) {
                $shops->where('street_id', $request->get('street_id'));
            }
            if ($request->has('local_government_area_id')) {
                $shops->where('local_government_area_id', $request->get('local_government_area_id'));
            }
            return ShopResource::collection($shops->paginate());
        }

        if ($entity_type === 'signage') {
            $signages = Signage::query();
            if ($request->has('street_id')) {
                $signages->where('street_id', $request->get('street_id'));
            }
            if ($request->has('local_government_area_id')) {
                $signages->where('local_government_area_id', $request->get('local_government_area_id'));
                }
            return SignageResource::collection($signages->paginate());

        }

    }
}
