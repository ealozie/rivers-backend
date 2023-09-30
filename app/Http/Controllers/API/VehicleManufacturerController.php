<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleManufacturerResource;
use App\Http\Resources\VehicleModelResource;
use App\Models\VehicleManufacturer;
use Illuminate\Http\Request;

/**
 * @tags Vehicle Manufacturers Service
 */
class VehicleManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicle_manufacturers = VehicleManufacturer::all();
        return VehicleManufacturerResource::collection($vehicle_manufacturers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Return single resource & related vehicle models.
     */
    public function show(string $id)
    {
        $vehicle_manufacturer = VehicleManufacturer::findOrFail($id);
        return response()->json([
            'data' => [
                'vehicle_manufacturer' => new VehicleManufacturerResource($vehicle_manufacturer),
                'vehicle_models' => VehicleModelResource::collection($vehicle_manufacturer->vehicle_models),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
