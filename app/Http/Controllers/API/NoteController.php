<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NoteStoreRequest;
use App\Http\Resources\NoteResource;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Note;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use Illuminate\Http\Request;

/**
 * @tags Note Service
 */
class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::paginate();
        return NoteResource::collection($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NoteStoreRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['added_by'] = $request->user()->id;
        $entity_id = $validatedData['entity_id'];
        if ($validatedData['entity_type'] == 'property') {
            $property = Property::where('property_id', $entity_id)->first();
            if ($property) {
                $note = $property->notes()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found',
                ], 404);
            }
        }

        if ($validatedData['entity_type'] == 'shop') {
            $shop = Shop::where('shop_id', $entity_id)->first();
            if ($shop) {
                $note = $shop->notes()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shop not found',
                ], 404);
            }
        }
        if ($validatedData['entity_type'] == 'individual') {
            $individual = Individual::where('individual_id', $entity_id)->first();
            if ($individual) {
                $note = $individual->notes()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual not found',
                ], 404);
            }
        }
        if ($validatedData['entity_type'] == 'cooperate') {
            $cooperate = Cooperate::where('cooperate_id', $entity_id)->first();
            if ($cooperate) {
                $note = $cooperate->notes()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate not found',
                ], 404);
            } 
        }

        if ($validatedData['entity_type'] == 'signage') {
            $signage = Signage::where('signage_id', $entity_id)->first();
            if ($signage) {
                $note = $signage->notes()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Signage not found',
                ], 404);
            } 
        }
        if ($validatedData['entity_type'] == 'vehicle') {
            $vehicle = CommercialVehicle::where('vehicle_id', $entity_id)->first();
            if ($vehicle) {
                $note = $vehicle->notes()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vehicle not found',
                ], 404);
            } 
        }
        return new NoteResource($note);
    }

    /**
     * Retrieve notes for a specified resource.
     */
    public function notes(Request $request, $entity_type, $entity_id)
    {
        $validatedData['entity_type'] = $entity_type;
        if ($validatedData['entity_type'] == 'property') {
            $property = Property::where('property_id', $entity_id)->first();
            if ($property) {
                $notes = $property->property_notes;
                return NoteResource::collection($notes);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found',
                ], 404);
            }
        }

        if ($validatedData['entity_type'] == 'shop') {
            $shop = Shop::where('shop_id', $entity_id)->first();
            if ($shop) {
                $notes = $shop->notes;
                return NoteResource::collection($notes);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shop not found',
                ], 404);
            }
        }
        if ($validatedData['entity_type'] == 'individual') {
            $individual = Individual::where('individual_id', $entity_id)->first();
            if ($individual) {
                $notes = $individual->notes;
                return NoteResource::collection($notes);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual not found',
                ], 404);
            }
        }
        if ($validatedData['entity_type'] == 'cooperate') {
            $cooperate = Cooperate::where('cooperate_id', $entity_id)->first();
            if ($cooperate) {
                $notes = $cooperate->notes;
                return NoteResource::collection($notes);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate not found',
                ], 404);
            } 
        }

        if ($validatedData['entity_type'] == 'signage') {
            $signage = Signage::where('signage_id', $entity_id)->first();
            if ($signage) {
                $notes = $signage->notes;
                return NoteResource::collection($notes);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Signage not found',
                ], 404);
            } 
        }
        if ($validatedData['entity_type'] == 'vehicle') {
            $vehicle = CommercialVehicle::where('vehicle_id', $entity_id)->first();
            if ($vehicle) {
                $notes = $vehicle->notes;
                return NoteResource::collection($notes);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vehicle not found',
                ], 404);
            } 
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $note = Note::findOrFail($id);
        return new NoteResource($note);
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
        Note::destroy($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Note deleted successfully',
        ], 200);
    }
}
