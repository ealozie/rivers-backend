<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpouseStoreRequest;
use App\Http\Resources\SpouseResource;
use App\Models\Spouse;
use Illuminate\Http\Request;

/**
 * @tags Spouse Detail Service
 */
class SpouseController extends Controller
{
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
    public function store(SpouseStoreRequest $request)
    {
        $validatedData = $request->validated();
        $spouse = Spouse::firstOrCreate($validatedData);
        return response()->json(['status' => 'success', 'data' => new SpouseResource($spouse)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
