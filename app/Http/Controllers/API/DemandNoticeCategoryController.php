<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandNoticeCategoryStoreRequest;
use App\Http\Requests\DemandNoticeCategoryUpdateRequest;
use App\Http\Resources\DemandNoticeCategoryResource;
use App\Models\DemandNoticeCategory;

/**
 * @tags Demand Notice Category Service
 */
class DemandNoticeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $demand_notice_categories = DemandNoticeCategory::all();
        return DemandNoticeCategoryResource::collection($demand_notice_categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DemandNoticeCategoryStoreRequest $request)
    {
        $validated = $request->validated();
        $demand_notice_category = DemandNoticeCategory::create($validated);
        return new DemandNoticeCategoryResource($demand_notice_category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $demand_notice_category = DemandNoticeCategory::findOrFail($id);
        return new DemandNoticeCategoryResource($demand_notice_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DemandNoticeCategoryUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $demand_notice_category = DemandNoticeCategory::findOrFail($id);
        $demand_notice_category->update($validated);
        return new DemandNoticeCategoryResource($demand_notice_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $demand_notice_category = DemandNoticeCategory::findOrFail($id);
        $demand_notice_category->delete();
        return response()->json(['status' => 'success', 'message' => 'Demand Notice Category deleted successfully']);
    }
}
