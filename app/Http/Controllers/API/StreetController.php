<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StreetStoreRequest;
use App\Http\Requests\StreetUpdateRequest;
use App\Http\Resources\StreetResource;
use App\Models\Street;
use Illuminate\Http\Request;

class StreetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('store', 'update', 'destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * Query params:
     * - search: string
     * - local_government_area_id: int
     */
    public function index(Request $request)
    {
        $streets = Street::query();
        if ($request->has('search')) {
            $streets->where('name', 'like', '%' . $request->search . '%');
        }
        //if local government id is set
        if ($request->has('local_government_area_id')) {
            $streets->where('local_government_area_id', $request->local_government_area_id);
        }
        return StreetResource::collection($streets->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StreetStoreRequest $request)
    {
        $street = Street::create($request->validated());
        return new StreetResource($street);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $street = Street::findOrFail($id);
        return new StreetResource($street);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StreetUpdateRequest $request, string $id)
    {
        $street = Street::findOrFail($id);
        $street->update($request->validated());
        return new StreetResource($street);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $street = Street::findOrFail($id);
        $street->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Street deleted successfully'
        ], 204);
    }
}
