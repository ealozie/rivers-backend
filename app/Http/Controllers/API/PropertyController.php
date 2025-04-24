<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyStoreRequest;
use App\Http\Requests\PropertyUpdateRequest;
use App\Models\AccountManager;
use App\Models\Cooperate;
use App\Models\Individual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\PropertyPicture;
use App\Traits\PropertyAuthorizable;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Property Enumeration Service
 */
class PropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('index');
    }
    //use PropertyAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * Query Parameter `filter=count|lga|street` and `per_page=20`
     *
     * Additional query param: `local_government_area_id` & `street_id`
     */
    public function index(Request $request)
    {
        $per_page = 20;
        $user = $request->user();
        if ($request->has('per_page')) {
            $per_page = $request->get('per_page');
        }
        if ($request->has('filter') && $request->get('filter') == 'count') {
            $property_count = Property::where('approval_status', 'approved')->count();
            return response()->json([
                'status' => 'success',
                'message' => 'Property retrieved successfully.',
                'data' => [
                    'property_count' => $property_count
                ]
            ]);
        } else {
            if ($user->hasRole('account_officer')) {
                $property_ids = AccountManager::where('user_id', $user->id)
                    ->where('accountable_type', Property::class)
                    ->pluck('accountable_id')
                    ->toArray();
                $properties = Property::whereIn('id', $property_ids)->latest()->paginate($per_page);
            } else {
                $properties = Property::paginate($per_page);
            }
            if ($request->has('filter') && in_array($request->get('filter'), ['lga', 'street'])) {
                    if ($request->get('filter') == 'street') {
                        $street_id = $request->get('street_id');
                        $properties = Property::where('street_id', $street_id)->paginate($per_page);
                    }
                    if ($request->get('filter') == 'lga') {
                        $local_government_area_id = $request->get('local_government_area_id');
                        $properties = Property::where('local_government_area_id', $local_government_area_id)->paginate($per_page);
                    }
                }
            return response()->json([
                'status' => 'success',
                'message' => 'Properties retrieved successfully.',
                'data' => [
                    'properties' => PropertyResource::collection($properties),
                ]
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PropertyStoreRequest $request)
    {
        $validatedData = $request->validated();
        if (isset($validatedData['user_id'])) {
            $user = User::where('unique_id', $validatedData['user_id'])->first();
            if ( ! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User ID not found.',
                ], 404);
            }
            $validatedData['user_id'] = $user->id;
        }
        if ( ! isset($validatedData['demand_notice_category_id'])) {
            $validatedData['demand_notice_category_id'] = 0;
        }
        if ($request->bearerToken()) {
            Auth::setUser($request->user('sanctum'));
            if ($request->user() && $request->user()->hasRole('admin')) {
                $validatedData['approval_status'] = 'approved';
            }
        }
        DB::beginTransaction();
        try {
            $validatedData['property_id'] = '4'.date('hi').mt_rand(11111, 99999);
            $property = Property::create($validatedData);
            if ($request->hasFile('property_pictures') && count($validatedData['property_pictures'])) {
                $property_images = $validatedData['property_pictures'];
                foreach ($property_images as $property_image) {
                    $path = $property_image->store('property_pictures', 'public');
                    $property_picture = new PropertyPicture();
                    $property_picture->property_id = $property->id;
                    $property_picture->picture_path = "/storage/".$path;
                    $property_picture->save();
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Property has been successfully enumerated.', 'data' => new PropertyResource($property)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to generate property ID',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove Property picture specified resource.
     */
    public function destroy_property_picture(string $id)
    {
        PropertyPciture::destroy($id);
        return response()->json(
            [
                "status" => "success",
                "message" => "Property picture deleted successfully.",
            ],
            200
        );
    }

    /**
     * Link Account the specified resource.
     */
    public function link_account(Request $request, $property_id)
    {
        $validatedData = $request->validate([
            'individual_id_or_cooperate_id' => 'required|min:10|max:10'
        ]);
        $property = Property::find($property_id);
        if ( ! $property) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property ID not found.'
            ], 404);
        }
        $user_id_prefix = $validatedData['individual_id_or_cooperate_id'][0];
        if ($user_id_prefix == 2) {
            $cooperate = Cooperate::where('cooperate_id', $validatedData['individual_id_or_cooperate_id'])->first();
            if ( ! $cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate ID not found.'
                ], 404);
            }
            $user = User::find($cooperate->user_id);
        } else {
            if ($user_id_prefix == 1) {
                $individual = Individual::where('individual_id',
                    $validatedData['individual_id_or_cooperate_id'])->first();
                //return $individual;
                if ( ! $individual) {
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
        }
        //return $user;
        DB::beginTransaction();
        try {
            $property->update([
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
            'message' => 'Property linked successfully.',
            'data' => new PropertyResource($property)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::find($id);
        if ( ! $property) {
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
        $validatedData = $request->validated();
        if (isset($validatedData['user_id'])) {
            $user = User::where('unique_id', $validatedData['user_id'])->first();
            if ( ! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User ID not found.',
                ], 404);
            }
            $validatedData['user_id'] = $user->id;
        }
        DB::beginTransaction();
        try {
            $property = Property::find($id);
            if ( ! $property) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No property found.',
                ], 404);
            }
            //$validatedData['property_id'] = '4' . date('hi') . mt_rand(11111, 99999);
            $property->update($validatedData);
            if ($request->hasFile('property_pictures') && count($validatedData['property_pictures'])) {
                $property_images = $validatedData['property_pictures'];
                foreach ($property_images as $property_image) {
                    $path = $property_image->store('property_pictures', 'public');
                    $property_picture = new PropertyPicture();
                    $property_picture->property_id = $property->id;
                    $property_picture->picture_path = "/storage/".$path;
                    $property_picture->save();
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Property has been successfully enumerated.',
                'data' => new PropertyResource($property)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to update property.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Property::destroy($id);
        return response()->json([
            "status" => "success",
            "message" => "Property deleted successfully",
        ]);
    }

     /**
     * Remove the Owner from Property.
     */
    public function remove_owner(string $id)
    {
        try {
            $property = Property::findOrFail($id);
            if ($property) {
                $property->user_id = null;
                $property->save();
            }
        } catch (Exception $e) {
            return response()->json([
            "status" => "error",
            "message" => $e->getMessage(),
        ], 500);
        }
        return response()->json([
            "status" => "success",
            "message" => "Property owner removed successfully",
            'data' => new PropertyResource($property),
        ]);
    }

    /**
     * Get Properties by User ID or User Unique ID.
     */
    public function show_by_user_id(string $user_id_or_unique_id)
    {
        $user = User::where('id', $user_id_or_unique_id)->orWhere('unique_id', $user_id_or_unique_id)->first();
        if ( ! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $property = Property::where('user_id', $user->id)->get();
        if ( ! count($property)) {
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
            $individual_registrations = Property::with('user')->where('property_category_id',
                $query_request)->paginate($per_page);
        }
        if ($request->has('property_type_id')) {
            $query_request = $request->get('property_type_id');
            $individual_registrations = Property::with('user')->where('property_type_id',
                $query_request)->paginate($per_page);
        }
        if ($request->has('local_government_area_id')) {
            $query_request = $request->get('local_government_area_id');
            $individual_registrations = Property::with('user')->where('local_government_area_id',
                $query_request)->paginate($per_page);
        }
        if ($request->has('property_use_id')) {
            $query_request = $request->get('property_use_id');
            $individual_registrations = Property::with('user')->where('property_use_id',
                $query_request)->paginate($per_page);
        }
        if ($request->has('demand_notice_category_id')) {
            $query_request = $request->get('demand_notice_category_id');
            $individual_registrations = Property::with('user')->where('demand_notice_category_id',
                $query_request)->paginate($per_page);
        }
        if ($request->has('has_borehole')) {
            $query_request = $request->get('has_borehole');
            $individual_registrations = Property::with('user')->where('has_borehole',
                $query_request)->paginate($per_page);
        }

        if ($request->has('is_connected_to_power')) {
            $query_request = $request->get('is_connected_to_power');
            $individual_registrations = Property::with('user')->where('is_connected_to_power',
                $query_request)->paginate($per_page);
        }

        if ($request->has('property_id')) {
            $query_request = $request->get('property_id');
            $individual_registrations = Property::with('user')->where('property_id',
                $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $individual_registrations = Property::with('user')->whereBetween('created_at',
                [$date_from, $date_to])->paginate($per_page);
        }
        if ( ! isset($individual_registrations)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return PropertyResource::collection($individual_registrations);
    }
}
