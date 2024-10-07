<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategoryRequestStore;
use App\Http\Requests\ServiceCategoryRequestUpdate;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;

/**
 * @tags Service Category Service
 */
class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service_categories = ServiceCategory::all();
        return ServiceCategoryResource::collection($service_categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceCategoryRequestStore $request)
    {
        $requestData = $request->validated();
        $requestData['created_by'] = auth()->id();
        $service_category = ServiceCategory::create($requestData);
        return new ServiceCategoryResource($service_category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_category = ServiceCategory::findOrFail($id);
        return new ServiceCategoryResource($service_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceCategoryRequestUpdate $request, string $id)
    {
        $requestData = $request->validated();
        $service_category = ServiceCategory::findOrFail($id);
        $service_category->update($requestData);
        return new ServiceCategoryResource($service_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service_category = ServiceCategory::findOrFail($id);
        $service_category->delete();
        return response()->json(['message' => 'Service sub category deleted successfully']);
    }
}
