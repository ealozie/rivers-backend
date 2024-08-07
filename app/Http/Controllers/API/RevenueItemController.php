<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RevenueItemStoreRequest;
use App\Http\Requests\RevenueItemUpdateRequest;
use App\Http\Resources\RevenueItemResource;
use App\Models\RevenueItem;

/**
 * @tags Revenue Item Service
 */
class RevenueItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $revenue_items = RevenueItem::all();
        return RevenueItemResource::collection($revenue_items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RevenueItemStoreRequest $request)
    {
        $validatedData = $request->validated();
        //$validatedData['added_by'] = $request->user()->id;
        $validatedData['unique_code'] = uniqid('rev_');
        $revenue_item = RevenueItem::create($validatedData);
        return new RevenueItemResource($revenue_item);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $revenue_item = RevenueItem::findOrFail($id);
        return new RevenueItemResource($revenue_item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RevenueItemUpdateRequest $request, string $id)
    {
        $revenue_item = RevenueItem::findOrFail($id);
        $revenue_item->update($request->validated());
        return new RevenueItemResource($revenue_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $revenue_item = RevenueItem::findOrFail($id);
        $revenue_item->delete();
        return response()->json(['status' => 'success', 'message' => 'Revenue Item deleted successfully.']);
    }
    /**
     * Return Revenue items by its Agency ID.
     */
    public function revenue_item_agency($agency_id)
    {
        $revenue_items = RevenueItem::where('agency_id', $agency_id)->get();
        return RevenueItemResource::collection($revenue_items);
    }
}
