<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandNoticeCategoryItemStoreRequest;
use App\Http\Resources\DemandNoticeCategoryItemResource;
use App\Models\DemandNoticeCategoryItem;
use App\Models\RevenueItem;
use Illuminate\Http\Request;

/**
 * @tags Demand Notice Category Item Service
 */
class DemandNoticeCategoryItemController extends Controller
{
    //use DemandNoticeCategoryItemAuthorizable;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $demand_notice_category_items = DemandNoticeCategoryItem::all();
        return DemandNoticeCategoryItemResource::collection($demand_notice_category_items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DemandNoticeCategoryItemStoreRequest $request)
    {

        //Prevent duplicate
        $requestData = $request->validated();
        //$requestData['amount'] = $revenue_item->fixed_fee;
        $requestData['added_by'] = $request->user()->id;
        $requestData['status'] = 'active';
        $demand_notice_category_item = DemandNoticeCategoryItem::where('revenue_item_id', $requestData['revenue_item_id'])->where('demand_notice_category_id', $requestData['demand_notice_category_id'])->first();
        if (!$demand_notice_category_item) {
            $demand_notice_category_item = DemandNoticeCategoryItem::create($requestData);
        }
        return new DemandNoticeCategoryItemResource($demand_notice_category_item);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $demand_notice_category_item = DemandNoticeCategoryItem::find($id);
        return new DemandNoticeCategoryItemResource($demand_notice_category_item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $requestData = $request->validated();
        $revenue_item = RevenueItem::find($requestData['revenue_item_id'])->first();
        $requestData['amount'] = $revenue_item->fixed_fee;
        $requestData['status'] = 'active';
        $demand_notice_category_item = DemandNoticeCategoryItem::find($id);
        $demand_notice_category_item->update($requestData);
        return new DemandNoticeCategoryItemResource($demand_notice_category_item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $demand_notice_category_item = DemandNoticeCategoryItem::find($id);
        $demand_notice_category_item->delete();
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Demand Notice Category Item deleted successfully'
            ]
        );
    }
}
