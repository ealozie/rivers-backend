<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyUseResource;
use App\Models\PropertyUse;
use Illuminate\Http\Request;

/**
 * @tags Property Use Service
 */
class PropertyUseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $property_uses = PropertyUse::all();
        return PropertyUseResource::collection($property_uses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
