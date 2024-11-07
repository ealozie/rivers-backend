<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceSubCategoryRequestStore;
use App\Http\Requests\ServiceSubCategoryRequestUpdate;
use App\Http\Resources\ServiceSubCategoryResource;
use App\Models\ServiceSubCategory;
use Illuminate\Http\Request;

/**
 * @tags Service Sub Category Service
 */
class ServiceSubCategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('store', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * Search by service category name `?name=`
     */
    public function index(Request $request)
    {
        if ($request->has('name')) {
            $name = $request->name;
            $service_sub_categories = ServiceSubCategory::where('name', 'like', "%{$name}%")->get();
        } else {
            $service_sub_categories = ServiceSubCategory::all();
        }
        return ServiceSubCategoryResource::collection($service_sub_categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceSubCategoryRequestStore $request)
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
