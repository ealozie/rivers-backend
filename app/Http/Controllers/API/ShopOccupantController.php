<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopOccupantStoreRequest;
use App\Http\Requests\ShopOccupantUpdateRequest;
use App\Http\Resources\ShopOccupantResource;
use App\Models\ShopOccupant;
use Illuminate\Http\Request;

class ShopOccupantController extends Controller
{
    const SUCCESS_STATUS = 200;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopOccupantStoreRequest $request)
    {
        $validateData = $request->validated();
        $shop_occupant = ShopOccupant::firstOrCreate($validateData, [
            'added_by' => $request->user()->id
        ]);
        return new ShopOccupantResource($shop_occupant);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop_occupant = ShopOccupant::findOrFail($id);
        return new ShopOccupantResource($shop_occupant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopOccupantUpdateRequest $request, string $id)
    {
        $validateData = $request->validated();
        $shop_occupant = ShopOccupant::findOrFail($id);
        $shop_occupant->update($validateData);
        return new ShopOccupantResource($shop_occupant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ShopOccupant::destroy($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Shop occupant removed successfully.'
        ], self::SUCCESS_STATUS);
    }
}
