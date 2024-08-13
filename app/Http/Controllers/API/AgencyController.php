<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgencyStoreRequest;
use App\Http\Requests\AgencyUpdateRequest;
use App\Http\Resources\AgencyResource;
use App\Traits\AgencyAuthorizable;
use App\Models\Agency;

/**
 * @tags Agency Service
 */
class AgencyController extends Controller
{
    use AgencyAuthorizable;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agencies = Agency::all();
        return AgencyResource::collection($agencies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgencyStoreRequest $request)
    {
        $validated = $request->validated();
        //$validated['added_by'] = $request->user()->id;
        $agency = Agency::create($validated);
        return new AgencyResource($agency);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $agency = Agency::findOrFail($id);
        return new AgencyResource($agency);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgencyUpdateRequest $request, string $id)
    {
        $requestData = $request->validated();
        $agency = Agency::findOrFail($id);
        $agency->update($requestData);
        return new AgencyResource($agency);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
