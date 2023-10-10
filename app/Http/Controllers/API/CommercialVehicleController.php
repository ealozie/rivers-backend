<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommercialVehicleStoreRequest;
use App\Http\Requests\CommercialVehicleUpdateRequest;
use App\Http\Resources\CommercialVehicleResource;
use App\Models\CommercialVehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Commercial Vehicle Enumeration Service
 */
class CommercialVehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commercial_vehicles = CommercialVehicle::latest()->get();
        return CommercialVehicleResource::collection($commercial_vehicles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommercialVehicleStoreRequest $request)
    {
        $validatedData = $request->validated();
        if (Auth::check()) {
            $validatedData['added_by'] = Auth::id();
        }
        $validatedData['status'] = 'pending';
        try {
            if ($request->hasFile('driver_license_image')) {
                $path = $request->file('driver_license_image')->store('commercial_vehicles', 'public');
                $validatedData['driver_license_image'] = "/storage/" . $path;
            }
            if ($request->hasFile('permit_image')) {
                $path = $request->file('permit_image')->store('commercial_vehicles', 'public');
                $validatedData['permit_image'] = "/storage/" . $path;
            }
            $validatedData['vehicle_id'] = '6' . date('hi') . mt_rand(11111, 99999);
            $commercial_vehicle = CommercialVehicle::create($validatedData);
            return response()->json([
                'status' => 'success',
                'message' => 'Commercial vehicle has been enumerated successfully.', 'data' => new CommercialVehicleResource($commercial_vehicle)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $commercial_vehicle = CommercialVehicle::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => new CommercialVehicleResource($commercial_vehicle)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommercialVehicleUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        //check if user is authenticated
        if (Auth::check()) {
            $validatedData['added_by'] = Auth::id();
        }

        try {
            if ($request->hasFile('driver_license_image')) {
                $path = $request->file('driver_license_image')->store('commercial_vehicles', 'public');
                $validatedData['driver_license_image'] = "/storage/" . $path;
            }
            if ($request->hasFile('permit_image')) {
                $path = $request->file('permit_image')->store('commercial_vehicles', 'public');
                $validatedData['permit_image'] = "/storage/" . $path;
            }
            $commercial_vehicle = CommercialVehicle::find($id);
            $commercial_vehicle->update($validatedData);
            return response()->json([
                'status' => 'success',
                'message' => 'Commercial vehicle enumeration has been updated successfully.',
                'data' => new CommercialVehicleResource($commercial_vehicle)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get Commercial Vehicles by User ID or User Unique ID.
     */
    public function show_by_user_id(string $user_id_or_unique_id)
    {
        $user = User::where('unique_id', $user_id_or_unique_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $commercial_vehicles = CommercialVehicle::where('user_id', $user->id)->get();
        if (!count($commercial_vehicles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Commercial vehicle not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Commercial vehicle retrieved successfully.',
            'data' => CommercialVehicleResource::collection($commercial_vehicles)
        ]);
    }
}
