<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Models\User;

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
        $shops = Shop::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Shops retrieved successfully.',
            'data' => ShopResource::collection($shops)
        ]);
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
            'message' => 'Shop retrieved successfully.',
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
            'message' => 'Shop updated successfully.',
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

    /**
     * Get Shop by User ID or User Unique ID.
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
        $shop = Shop::where('user_id', $user->id)->get();
        if (!count($shop)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Shop retrieved successfully.',
            'data' => ShopResource::collection($shop)
        ]);
    }
}
