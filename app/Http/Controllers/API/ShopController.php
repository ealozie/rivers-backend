<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Traits\ShopAuthorizable;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Shop Service
 */
class ShopController extends Controller
{
    use ShopAuthorizable;
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
        $validatedData['shop_id'] = '3' . date('hi') . mt_rand(11111, 99999);
        $user_id = $validatedData['user_id'];
        $user = User::where('unique_id', $user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $validatedData['user_id'] = $user->id;
        $validatedData['added_by'] = $request->user()->id;
        try {
            $shop = Shop::create($validatedData);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not added.',
                'error' => $e->getMessage()
            ], 500);
        }
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

    /**
     * Advanced Search in resource.
     *
     * Query paramters `name` or `shop_number`.<br>
     * Additonal Query paramters `market_name_id`, `local_government_area_id`, `business_category_id`, `business_sub_category_id`, `classification_id`, `shop_id`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $per_page = 20;
        if ($request->has('name')) {
            $query_request = $request->get('name');
            $individual_registrations = Shop::with('user')->where('name', 'like', "%{$query_request}%")->paginate($per_page);
        }
        if ($request->has('shop_number')) {
            $query_request = $request->get('shop_number');
            $individual_registrations = Shop::with('user')->where('shop_number', 'like', "%{$query_request}%")->paginate($per_page);
        }
        if ($request->has('market_name_id')) {
            $query_request = $request->get('market_name_id');
            $individual_registrations = Shop::with('user')->where('market_name_id', $query_request)->paginate($per_page);
        }
        if ($request->has('local_government_area_id')) {
            $query_request = $request->get('local_government_area_id');
            $individual_registrations = Shop::with('user')->where('local_government_area_id', $query_request)->paginate($per_page);
        }
        if ($request->has('business_category_id')) {
            $query_request = $request->get('business_category_id');
            $individual_registrations = Shop::with('user')->where('business_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('business_sub_category_id')) {
            $query_request = $request->get('business_sub_category_id');
            $individual_registrations = Shop::with('user')->where('business_sub_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('classification_id')) {
            $query_request = $request->get('classification_id');
            $individual_registrations = Shop::with('user')->where('classification_id', $query_request)->paginate($per_page);
        }

        if ($request->has('shop_id')) {
            $query_request = $request->get('shop_id');
             $individual_registrations = Shop::with('user')->where('shop_id', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $individual_registrations = Shop::with('user')->whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($individual_registrations)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return ShopResource::collection($individual_registrations);
    }
}
