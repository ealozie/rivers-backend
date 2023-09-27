<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyStoreRequest;
use App\Http\Requests\PropertyUpdateRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\PropertyPicture;
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
        //
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
}
