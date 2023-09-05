<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;

/**
 * @tags Shop Service
 */
class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopStoreRequest $request)
    {
        $validatedData = $request->validated();
        $shop = Shop::firstOrCreate($validatedData);
        return response()->json([
            'status' => 'success',
            'message' => 'Shop added successfully.',
            'data' => new ShopResource($shop)
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Shop added successfully.',
            'data' => new ShopResource($shop)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $shop = Shop::find($id);
        $shop->update($validatedData);
        return response()->json([
            'status' => 'success',
            'message' => 'Shop added successfully.',
            'data' => new ShopResource($shop)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
