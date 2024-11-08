<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceProviderRequestStore;
use App\Http\Requests\ServiceProviderRequestUpdate;
use App\Http\Resources\ServiceProviderResource;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

/**
 * @tags Service Provider Service
 */
class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service_providers = ServiceProvider::all();
        return ServiceProviderResource::collection($service_providers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceProviderRequestStore $request)
    {
        $requestData = $request->validated();
        $requestData['created_by'] = auth()->id();
        $service_provider = ServiceProvider::create($requestData);
        return new ServiceProviderResource($service_provider);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_provider = ServiceProvider::findOrFail($id);
        return new ServiceProviderResource($service_provider);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceProviderRequestUpdate $request, string $id)
    {
        $requestData = $request->validated();
        $service_provider = ServiceProvider::findOrFail($id);
        $service_provider->update($requestData);
        return new ServiceProviderResource($service_provider);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service_provider = ServiceProvider::findOrFail($id);
        $service_provider->delete();
        return response()->json(['message' => 'Service Provider deleted successfully']);
    }
}
