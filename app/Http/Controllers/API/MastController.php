<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MastStoreRequest;
use App\Http\Requests\MastUpdateRequest;
use App\Http\Resources\MastListResource;
use App\Http\Resources\MastResource;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Mast;
use App\Models\MastPicture;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Query Params: `search`, `per_page`, `filter=count`
     */
    public function index(Request $request)
    {
        $search = $request->query("search");
        $per_page = $request->query("per_page", 10);
        if ($request->has('filter') && $request->get('filter') == 'count') {
            $mast_count = Mast::where('approval_status', 'approved')->count();
            return response()->json([
                'status' => 'success',
                'message' => 'Mast retrieved successfully.',
                'data' => [
                    'mast_count' => $mast_count
                ]
            ]);
        }
        $masts = Mast::query();
        if ($search) {
            $masts = $masts
                ->where("mast_name", "like", "%$search%")
                ->orWhere("mast_use", "like", "%$search%");
        }
        return MastListResource::collection($masts->paginate($per_page));
    }

    /**
     * Get Mast by User ID or User Unique ID.
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
        $mast = Mast::where('user_id', $user->id)->get();
        if (!count($mast)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mast not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Mast retrieved successfully.',
            'data' => MastListResource::collection($mast)
        ]);
    }

    /**
     * Get Mast by Property ID.
     */
    public function show_by_property_id(string $property_id)
    {
        $property = Property::where('property_id', $property_id)->first();
        if (!$property) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property ID not found.',
            ], 404);
        }
        $mast = Mast::where('property_id', $property_id)->get();
        if (!count($mast)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mast not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Mast retrieved successfully.',
            'data' => MastListResource::collection($mast)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MastStoreRequest $request)
    {
        $validatedData = $request->validated();

        if (isset($validatedData["owner_id"])) {
            $owner = User::where(
                "unique_id",
                $validatedData["owner_id"]
            )->first();
            if (!$owner) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Owner not found",
                    ],
                    404
                );
            }
            $validatedData["owner_id"] = $owner->id;
        }
        DB::beginTransaction();
        try {
            if ($request->bearerToken()) {
                Auth::setUser($request->user("sanctum"));
                if ($request->user() && $request->user()->hasRole("admin")) {
                    $validatedData["approval_status"] = "approved";
                    $validatedData["created_by"] = $request->user()->id;
                }
            }
            $validatedData["mast_id"] =
                "8" . date("hi") . mt_rand(11111, 99999);
            $mast = Mast::create($validatedData);
            if (
                $request->hasFile("pictures") &&
                count($validatedData["pictures"])
            ) {
                $mast_images = $validatedData["pictures"];
                foreach ($mast_images as $mast_image) {
                    $path = $mast_image->store("mast_pictures", "public");
                    $mast_picture = new MastPicture();
                    $mast_picture->mast_id = $mast->id;
                    $mast_picture->image_path = "/storage/" . $path;
                    $mast_picture->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
        return new MastResource($mast);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mast = Mast::with("pictures")->find($id);
        if (!$mast) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Mast not found",
                ],
                404
            );
        }
        return new MastResource($mast);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MastUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $mast = Mast::find($id);
        if (!$mast) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Mast not found",
                ],
                404
            );
        }
        if (isset($validatedData["owner_id"])) {
            $owner = User::where(
                "unique_id",
                $validatedData["owner_id"]
            )->first();
            if (!$owner) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Owner not found",
                    ],
                    404
                );
            }
            $validatedData["owner_id"] = $owner->id;
        }
        if (isset($validatedData["property_id"])) {
            $property = Property::where(
                "property_id",
                $validatedData["property_id"]
            )->first();
            if (!$property) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Property not found",
                    ],
                    404
                );
            }
        }
        DB::beginTransaction();
        try {
            $mast->update($validatedData);
            if (
                $request->hasFile("pictures") &&
                count($validatedData["pictures"])
            ) {
                $mast_images = $validatedData["pictures"];
                foreach ($mast_images as $mast_image) {
                    $path = $mast_image->store("mast_pictures", "public");
                    $mast_picture = new MastPicture();
                    $mast_picture->mast_id = $mast->id;
                    $mast_picture->image_path = "/storage/" . $path;
                    $mast_picture->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
        return new MastResource($mast);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mast = Mast::find($id);
        if (!$mast) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Mast not found",
                ],
                404
            );
        }
        $mast->delete();
        return response()->json([
            "status" => "success",
            "message" => "Mast deleted successfully",
        ]);
    }

    /**
     * Link Account the specified resource.
     */
    public function link_account(Request $request, $mast_id)
    {
        $validatedData = $request->validate([
            "individual_id_or_cooperate_id" => "required|min:10|max:10",
        ]);
        $mast = Mast::find($mast_id);
        if (!$mast) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Mast ID not found.",
                ],
                404
            );
        }
        $user_id_prefix = $validatedData["individual_id_or_cooperate_id"][0];
        if ($user_id_prefix == 2) {
            $cooperate = Cooperate::where(
                "cooperate_id",
                $validatedData["individual_id_or_cooperate_id"]
            )->first();
            if (!$cooperate) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Cooperate ID not found.",
                    ],
                    404
                );
            }
            $user = User::find($cooperate->user_id);
        } elseif ($user_id_prefix == 1) {
            $individual = Individual::where(
                "individual_id",
                $validatedData["individual_id_or_cooperate_id"]
            )->first();
            //return $individual;
            if (!$individual) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Individual ID not found.",
                    ],
                    404
                );
            }
            $user = User::find($individual->user_id);
        } else {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "User ID not found.",
                ],
                404
            );
        }
        //return $user;
        DB::beginTransaction();
        try {
            $mast->update([
                "owner_id" => $user->id,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
        return response()->json(
            [
                "status" => "success",
                "message" => "Mast linked successfully.",
                "data" => new MastResource($mast),
            ],
            200
        );
    }
}
