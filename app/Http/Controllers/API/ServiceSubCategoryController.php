<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceSubCategoryRequestUpdate;
use App\Http\Resources\ServiceSubCategoryResource;
use App\Models\ServiceSubCategory;

/**
 * @tags Service Sub Category Service
 */
class ServiceSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service_sub_categories = ServiceSubCategory::all();
        return ServiceSubCategoryResource::collection($service_sub_categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceSubCategoryRequestUpdate $request)
    {
        $requestData = $request->validated();
        $requestData['created_by'] = auth()->id();
        $service_sub_category = ServiceSubCategory::create($requestData);
        return new ServiceSubCategoryResource($service_sub_category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_sub_category = ServiceSubCategory::findOrFail($id);
        return new ServiceSubCategoryResource($service_sub_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceSubCategoryRequestUpdate $request, string $id)
    {
        $requestData = $request->validated();
        $service_sub_category = ServiceSubCategory::findOrFail($id);
        $service_sub_category->update($requestData);
        return new ServiceSubCategoryResource($service_sub_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service_sub_category = ServiceSubCategory::findOrFail($id);
        $service_sub_category->delete();
        return response()->json(['message' => 'Service sub category deleted successfully']);
    }
}
