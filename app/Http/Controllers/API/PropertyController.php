<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyStoreRequest;
use App\Http\Requests\PropertyUpdateRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\PropertyPicture;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Property Enumeration Service
 */
class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Properties retrieved successfully.',
            'data' => PropertyResource::collection($properties)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PropertyStoreRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $validatedData['property_id'] = '4' . date('hi') . mt_rand(11111, 99999);
            $property = Property::create($validatedData);
            if ($request->hasFile('property_pictures') && count($validatedData['property_pictures'])) {
                $property_images = $validatedData['property_pictures'];
                foreach ($property_images as $property_image) {
                    $path = $property_image->store('property_pictures', 'public');
                    $property_picture = new PropertyPicture();
                    $property_picture->property_id = $property->id;
                    $property_picture->picture_path = "/storage/" . $path;
                    $property_picture->save();
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Property has been successfully enumerated.', 'data' => new PropertyResource($property)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to generate property ID',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json([
                'status' => 'error',
                'message' => 'No property found.',
            ], 404);
        }

        return new PropertyResource($property);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PropertyUpdateRequest $request, string $id)
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

    /**
     * Get Properties by User ID or User Unique ID.
     */
    public function show_by_user_id(string $user_id_or_unique_id)
    {
        $user = User::where('id', $user_id_or_unique_id)->orWhere('unique_id', $user_id_or_unique_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $property = Property::where('user_id', $user->id)->get();
        if (!count($property)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Property retrieved successfully.',
            'data' => PropertyResource::collection($property)
        ]);
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `property_category_id` or `property_type_id`.<br>
     * Additonal Query paramters `local_government_area_id`, `is_connected_to_power`, `property_use_id`, `demand_notice_category_id`, `date_from and date_to`, `has_borehole`, `property_id`
     */
    public function search(Request $request)
    {
        $per_page = 20;
        if ($request->has('property_category_id')) {
            $query_request = $request->get('property_category_id');
            $individual_registrations = Property::with('user')->where('property_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('property_type_id')) {
            $query_request = $request->get('property_type_id');
            $individual_registrations = Property::with('user')->where('property_type_id', $query_request)->paginate($per_page);
        }
        if ($request->has('local_government_area_id')) {
            $query_request = $request->get('local_government_area_id');
            $individual_registrations = Property::with('user')->where('local_government_area_id', $query_request)->paginate($per_page);
        }
        if ($request->has('property_use_id')) {
            $query_request = $request->get('property_use_id');
            $individual_registrations = Property::with('user')->where('property_use_id', $query_request)->paginate($per_page);
        }
        if ($request->has('demand_notice_category_id')) {
            $query_request = $request->get('demand_notice_category_id');
            $individual_registrations = Property::with('user')->where('demand_notice_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('has_borehole')) {
            $query_request = $request->get('has_borehole');
            $individual_registrations = Property::with('user')->where('has_borehole', $query_request)->paginate($per_page);
        }

        if ($request->has('is_connected_to_power')) {
            $query_request = $request->get('is_connected_to_power');
            $individual_registrations = Property::with('user')->where('is_connected_to_power', $query_request)->paginate($per_page);
        }
        
        if ($request->has('property_id')) {
            $query_request = $request->get('property_id');
             $individual_registrations = Property::with('user')->where('property_id', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $individual_registrations = Property::with('user')->whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($individual_registrations)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return PropertyResource::collection($individual_registrations);
    }
}
