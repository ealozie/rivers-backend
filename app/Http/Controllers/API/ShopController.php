<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Http\Resources\ShopResource;
use App\Models\AccountManager;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\User;
use App\Traits\ShopAuthorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Shop Service
 */
class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('index');
    }
    //use ShopAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * Query Parameter `filter=count|lga|street` and `per_page=20`
     *
     * Additional query param: `local_government_area_id` & `street_id`
     */
    public function index(Request $request)
    {
        $per_page = 100;
        $user = $request->user();
        if ($request->has('per_page')) {
            $per_page = $request->get('per_page');
        }
        if ($request->has('filter') && $request->get('filter') == 'count') {
            $shops_count = Shop::where('approval_status', 'approved')->count();
            return response()->json([
            'status' => 'success',
            'message' => 'Shops retrieved successfully.',
            'data' => [
                'shops_count' => $shops_count
            ]
        ]);
        } else {
            if ($user->hasRole('account_officer')) {
                $shops_ids = AccountManager::where('user_id', $user->id)
                    ->where('accountable_type', Shop::class)
                    ->pluck('accountable_id')
                    ->toArray();
                $shops = Shop::whereIn('id', $shops_ids)->paginate($per_page);
            } else {
                $shops = Shop::paginate($per_page);
            }
            if ($request->has('filter') && in_array($request->get('filter'), ['lga', 'street'])) {
                    if ($request->get('filter') == 'street') {
                        $street_id = $request->get('street_id');
                        $shops = Shop::where('street_id', $street_id)->paginate($per_page);
                    }
                    if ($request->get('filter') == 'lga') {
                        $local_government_area_id = $request->get('local_government_area_id');
                        $shops = Shop::where('local_government_area_id', $local_government_area_id)->paginate($per_page);
                    }
                }
            return response()->json([
            'status' => 'success',
            'message' => 'Shops retrieved successfully.',
            'data' => [
                'shops' => ShopResource::collection($shops),
            ]
        ]);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopStoreRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['shop_id'] = '3' . date('hi') . mt_rand(11111, 99999);
        if (isset($validatedData['user_id'])) {
            $user_id = $validatedData['user_id'];
            $user = User::where('unique_id', $user_id)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User ID not found.',
                ], 404);
            }
            $validatedData['user_id'] = $user->id;
        }

        if ($request->bearerToken()) {
            Auth::setUser($request->user('sanctum'));
            if ($request->user() && $request->user()->hasRole('admin')) {
                $validatedData['added_by'] = $request->user()->id;
                $validatedData['approval_status'] = 'approved';
            }
        }
        DB::beginTransaction();
        try {
            $shop = Shop::create($validatedData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
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
     * Get Resources by Property ID.
     */
    public function get_by_property_id(Request $request, string $property_id)
    {
        $property = Property::where('property_id', $property_id)->first();
        if (!$property) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property ID not found',
            ], 404);
        }
        $shop = Shop::where('property_id', $property_id)->get();
        return ShopResource::collection($shop);
    }

    /**
     * Link Account the specified resource.
     */
    public function link_account(Request $request, $shop_id)
    {
        $validatedData = $request->validate([
            'individual_id_or_cooperate_id' => 'required|min:10|max:10'
        ]);
        $shop = Shop::find($shop_id);
        if (!$shop) {
            return response()->json([
                    'status' => 'error',
                    'message' => 'Shop ID not found.'
                ], 404);
        }
        $user_id_prefix = $validatedData['individual_id_or_cooperate_id'][0];
        if ($user_id_prefix == 2) {
            $cooperate = Cooperate::where('cooperate_id', $validatedData['individual_id_or_cooperate_id'])->first();
            if (!$cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate ID not found.'
                ], 404);
            }
            $user = User::find($cooperate->user_id);
        } else if ($user_id_prefix == 1) {
            $individual = Individual::where('individual_id', $validatedData['individual_id_or_cooperate_id'])->first();
            //return $individual;
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual ID not found.'
                ], 404);
            }
            $user = User::find($individual->user_id);
        } else {
            return response()->json([
                    'status' => 'error',
                    'message' => 'User ID not found.'
                ], 404);
        }
        //return $user;
        DB::beginTransaction();
        try {
            $shop->update([
                'user_id' => $user->id
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Shop linked successfully.',
            'data' => new ShopResource($shop)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        DB::beginTransaction();
        try {
            $shop = Shop::find($id);
            $shop->update($validatedData);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
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
        Shop::destroy($id);
        return response()->json([
            "status" => "success",
            "message" => "Shop deleted successfully",
        ]);
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
