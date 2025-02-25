<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignageStoreRequest;
use App\Http\Requests\SignageUpdateRequest;
use App\Http\Resources\SignageResource;
use App\Models\Signage;
use App\Models\User;
use App\Traits\SignageAuthorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function index()
    {
        $per_page = 20;
        if ($request->has('per_page')) {
            $per_page = $request->get('per_page');
        }
        if ($request->has('filter') && $request->get('filter') == 'count') {
            $signage_count = Signage::count();
            return response()->json([
            'status' => 'success',
            'message' => 'Signage retrieved successfully.',
            'data' => [
                'signage_count' => $signage_count
            ]
        ], 200);
        } else {
            $signage = Signage::latest()->paginate($per_page);
            return response()->json([
            'status' => 'success',
            'message' => 'Signage retrieved successfully.',
            'data' => [
                'signages' => SignageResource::collection($signage),
            ]
        ], 200);
        }
        //return SignageResource::collection($signage);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SignageStoreRequest $request)
    {
        $user = $request->user();
        $validatedData = $request->validated();
        $owner = User::where('unique_id', $validatedData['user_id'])->first();
        $validatedData['user_id'] = $owner->id;
        $validatedData['added_by'] = $user->id ?? 0;
        $validatedData['signage_id'] = '5' . date('hi') . mt_rand(11111, 99999);
        $signage = Signage::create($validatedData);
        return new SignageResource($signage);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $signage = Signage::find($id);
        if (!$signage) {
            return response()->json(['status' => 'error', 'message' => 'Signage not found'], 404);
        }
        return new SignageResource($signage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SignageUpdateRequest $request, string $id)
    {
        $user = $request->user();
        $validatedData = $request->validated();
        $validatedData['added_by'] = $user->id ?? 0;
        $signage = Signage::find($id);
        $signage->update($validatedData);
        return new SignageResource($signage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Signage::destroy($id);
        return response()->json(['status' => 'success', 'message' => 'Signage deleted successfully',], 200);
    }

    /**
     * Get Signage by User ID or User Unique ID.
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
        $signage = Signage::where('user_id', $user->id)->get();
        if (!count($signage)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Signage not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Signage retrieved successfully.',
            'data' => SignageResource::collection($signage)
        ]);
    }
}
