<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignageStoreRequest;
use App\Http\Requests\SignageUpdateRequest;
use App\Http\Resources\SignageResource;
use App\Models\Signage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Signage Service
 */
class SignageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $signage = Signage::paginate();
        return SignageResource::collection($signage);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SignageStoreRequest $request)
    {
        $user = $request->user();
        $validatedData = $request->validated();
        $validatedData['added_by'] = $user->id ?? 0;
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
}
