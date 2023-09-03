<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResidentialStoreRequest;
use App\Http\Resources\ResidentialResource;
use App\Models\Residential;

/**
 * @tags Residential Address Service
 */
class ResidentialController extends Controller
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
    public function store(ResidentialStoreRequest $request)
    {
        $validatedData = $request->validated();
        $residential_address = Residential::firstOrCreate($validatedData);
        return response()->json(['status' => 'success', 'data' => new ResidentialResource($residential_address)]);
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
