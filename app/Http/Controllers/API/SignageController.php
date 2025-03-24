<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignageStoreRequest;
use App\Http\Requests\SignageUpdateRequest;
use App\Http\Resources\SignageResource;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Signage;
use App\Models\User;
use App\Traits\SignageAuthorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @tags Signage Service
 */
class SignageController extends Controller
{
    //use SignageAuthorizable;

    /**
     * Display a listing of the resource.
     *
     * Query Parameter `filter=count` and `per_page=20`
     */
    public function index(Request $request)
    {
        $per_page = 20;
        if ($request->has("per_page")) {
            $per_page = $request->get("per_page");
        }
        if ($request->has("filter") && $request->get("filter") == "count") {
            $signage_count = Signage::count();
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Signage retrieved successfully.",
                    "data" => [
                        "signage_count" => $signage_count,
                    ],
                ],
                200
            );
        } else {
            $signage = Signage::latest()->paginate($per_page);
            return response()->json(
                [
                    "status" => "success",
                    "message" => "Signage retrieved successfully.",
                    "data" => [
                        "signages" => SignageResource::collection($signage),
                    ],
                ],
                200
            );
        }
        //return SignageResource::collection($signage);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SignageStoreRequest $request)
    {
        $validatedData = $request->validated();
        if (isset($validatedData['user_id'])) {
            $owner = User::where("unique_id", $validatedData["user_id"])->first();
            if ($owner) {
                $validatedData["user_id"] = $owner->id;
            }
        }
        if (auth()->user()) {
            $validatedData["added_by"] = $request->user()->id;
            $validatedData["approval_status"] = "approved";
        }
        $validatedData["signage_id"] = "5" . date("hi") . mt_rand(11111, 99999);
        DB::beginTransaction();
        try {
            $signage = Signage::create($validatedData);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return new SignageResource($signage);
    }


    /**
     * Link Account the specified resource.
     */
    public function link_account(Request $request, $signage_id)
    {
        $validatedData = $request->validate([
            'individual_id_or_cooperate_id' => 'required|min:10|max:10'
        ]);
        $signage = Signage::find($signage_id);
        if (!$signage) {
            return response()->json([
                    'status' => 'error',
                    'message' => 'Signage ID not found.'
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
            $signage->update([
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
            'message' => 'Signage linked successfully.',
            'data' => new SignageResource($signage)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $signage = Signage::find($id);
        if (!$signage) {
            return response()->json(
                ["status" => "error", "message" => "Signage not found"],
                404
            );
        }
        return new SignageResource($signage);
    }

    /**
     * Get Resources by Property ID.
     */
    public function get_by_property_id(Request $request, string $property_id)
    {
        $property = Property::where("property_id", $property_id)->first();
        if (!$property) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Property ID not found",
                ],
                404
            );
        }
        $signages = Signage::where("property_id", $property_id)->get();
        return SignageResource::collection($signages);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SignageUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        if (auth()->user()) {
            $user = $request->user();
            $validatedData["added_by"] = $user->id;
        }
        DB::beginTransaction();
        try {
            $signage = Signage::find($id);
            $signage->update($validatedData);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return new SignageResource($signage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Signage::destroy($id);
        return response()->json(
            [
                "status" => "success",
                "message" => "Signage deleted successfully",
            ],
            200
        );
    }

    /**
     * Get Signage by User ID or User Unique ID.
     */
    public function show_by_user_id(string $user_id_or_unique_id)
    {
        $user = User::where("id", $user_id_or_unique_id)
            ->orWhere("unique_id", $user_id_or_unique_id)
            ->first();
        if (!$user) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "User ID not found.",
                ],
                404
            );
        }
        $signage = Signage::where("user_id", $user->id)->get();
        if (!count($signage)) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Signage not found.",
                ],
                404
            );
        }
        return response()->json([
            "status" => "success",
            "message" => "Signage retrieved successfully.",
            "data" => SignageResource::collection($signage),
        ]);
    }
}
