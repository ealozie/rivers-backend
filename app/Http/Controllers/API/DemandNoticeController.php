<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandNoticeStoreRequest;
use App\Http\Requests\DemandNoticeUpdateRequest;
use App\Http\Resources\DemandNoticeResource;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\DemandNotice;
use App\Models\DemandNoticeCategory;
use App\Models\DemandNoticeCategoryItem;
use App\Models\DemandNoticeItem;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use App\Models\User;
use App\Traits\DemandNoticeAuthorizable;
use Illuminate\Http\Request;

/**
 * @tags Demand Notice Service
 */
class DemandNoticeController extends Controller
{
    //use DemandNoticeAuthorizable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $demand_notices = DemandNotice::all();
        return DemandNoticeResource::collection($demand_notices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DemandNoticeStoreRequest $request)
    {
        $requestData = $request->validated();
        $requestData['demand_notice_number'] = 'DN-' . date('Y') . '-' . date('md') . '-' . rand(1000, 9999);
        $requestData['generated_by'] = $request->user()->id;
        // $user = User::where('unique_id', $requestData['user_id'])->first();
        // if (!$user) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'User not found.'
        //     ]);
        // }
        //check that demand notice hasn't been geneated before
        $requestData['user_id'] = $request->user()->id;
        if ($requestData['entity_type'] == 'shop') {
            $shop = Shop::where('shop_id', $requestData['entity_id'])->first();
            if (!$shop) {
                return response()->json([
                    'status' => 'error',
                    'message'=> 'Shop not found',
                ], 404);
            }
            $demand_notice = $shop->demand_notices()->create($requestData);
        }
        if ($requestData['entity_type'] == 'vehicle') {
            $vehicle = CommercialVehicle::where('vehicle_id', $requestData['entity_id'])->first();
            if (!$vehicle) {
                return response()->json([
                    'status' => 'error',
                    'message'=> 'Commercial Vehicle not found.',
                ], 404);
            }
            $demand_notice = $vehicle->demand_notices()->create($requestData);
        }
        if ($requestData['entity_type'] == 'property') {
            $property = Property::where('property_id', $requestData['entity_id'])->first();
            if (!$property) {
                return response()->json([
                    'status' => 'error',
                    'message'=> 'Property not found.',
                ], 404);
            }
            $demand_notice = $property->demand_notices()->create($requestData);
        }
        if ($requestData['entity_type'] == 'signage') {
            $signage = Signage::where('signage_id', $requestData['entity_id'])->first();
            if (!$signage) {
                return response()->json([
                    'status' => 'error',
                    'message'=> 'Signage not found.',
                ], 404);
            }
            $demand_notice = $signage->demand_notices()->create($requestData);
        }
        if ($requestData['entity_type'] == 'individual') {
            $individual = Individual::where('individual_id', $requestData['entity_id'])->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message'=> 'Individual not found.',
                ], 404);
            }
            $demand_notice = $individual->demand_notices()->create($requestData);
        }

        if ($requestData['entity_type'] == 'cooperate') {
            $cooperate = Cooperate::where('cooperate_id', $requestData['entity_id'])->first();
            if (!$cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message'=> 'Cooperate not found.',
                ], 404);
            }
            $demand_notice = $cooperate->demand_notices()->create($requestData);
        }
        $demand_notice_category_items = DemandNoticeCategoryItem::where('demand_notice_category_id', $requestData['demand_notice_category_id'])->get();
        foreach ($demand_notice_category_items as $demand_notice_category_item) {
            $demand_notice_item = new DemandNoticeItem();
            $demand_notice_item->demand_notice_id = $demand_notice->id;
            $demand_notice_item->year_id = $requestData['year_id'];
            $demand_notice_item->revenue_item_id = $demand_notice_category_item->revenue_item_id;
            $demand_notice_item->amount = $demand_notice_category_item->amount;
            $demand_notice_item->payment_status = 'pending';
            $demand_notice_item->save();
        }
        return new DemandNoticeResource($demand_notice);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $demand_notice = DemandNotice::findOrFail($id);
        return new DemandNoticeResource($demand_notice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DemandNoticeUpdateRequest $request, string $id)
    {
        $requestData = $request->validated();
        $demand_notice = DemandNotice::findOrFail($id);
        $requestData['has_been_served'] = true;
        $demand_notice_enforcement_duration = DemandNoticeCategory::findOrFail($demand_notice->demand_notice_category_id)->enforcement_duration;
        $requestData['enforcement_begins_at'] = date('Y-m-d', strtotime($requestData['date_served'] . ' + ' . $demand_notice_enforcement_duration . ' days'));
        $demand_notice->update($requestData);
        return new DemandNoticeResource($demand_notice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get Demand Notice by Demand Notice Number.
     */
    public function show_by_demand_notice_number(string $demand_notice_number)
    {
        $demand_notice = DemandNotice::where('demand_notice_number', $demand_notice_number)->first();
        if (!$demand_notice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Demand notice not found.'
            ], 404);
        }
        $demand_notices = DemandNotice::where('user_id', $demand_notice->user_id)->where('status', 'pending')->oldest()->get();
        return DemandNoticeResource::collection($demand_notices);
    }
}
