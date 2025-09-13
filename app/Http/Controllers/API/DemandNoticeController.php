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
use App\Models\Mast;
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

    public function __construct() {
        $this->middleware("auth:sanctum")->except(['show']);
    }
    //use DemandNoticeAuthorizable;

    /**
     * Display a listing of the resource. Query parameter ?demand_notice_type=blank|linked & per_page=10
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page ? $request->per_page : 50;
        if ($request->has("demand_notice_type") && $request->get("demand_notice_type") == 'blank') {
            $demand_notices = DemandNotice::where('demand_notice_type', 'blank')->paginate($per_page);
        } else {
            $demand_notices = DemandNotice::where('demand_notice_type', 'linked')->paginate($per_page);
        }
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
        $entity_prefix = 0;
        if (isset($requestData['entity_id'])) {
            $entity_prefix = $requestData['entity_id'][0];
        }

        if ($entity_prefix == 3 && $requestData['demand_notice_type'] == 'linked') {
            $shop = Shop::where('shop_id', $requestData['entity_id'])->first();
            if (!$shop) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shop not found',
                ], 404);
            }
            $demand_notice = DemandNotice::where('demand_noticeable_type', Shop::class)->where('demand_noticeable_id', $shop->id)->where('year_id', $requestData['year_id'])->first();
            if ($demand_notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demand Notice already exist for this year',
                ], 500);
            }
            $demand_notice = $shop->demand_notices()->create($requestData);
        }
        if ($entity_prefix == 6 && $requestData['demand_notice_type'] == 'linked') {
            $vehicle = CommercialVehicle::where('vehicle_id', $requestData['entity_id'])->first();
            if (!$vehicle) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Commercial Vehicle not found.',
                ], 404);
            }

            $demand_notice = DemandNotice::where('demand_noticeable_type', CommercialVehicle::class)->where('demand_noticeable_id', $vehicle->id)->where('year_id', $requestData['year_id'])->first();
            if ($demand_notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demand Notice already exist for this year',
                ], 500);
            }

            $demand_notice = $vehicle->demand_notices()->create($requestData);
        }
        if ($entity_prefix == 4 && $requestData['demand_notice_type'] == 'linked') {
            $property = Property::where('property_id', $requestData['entity_id'])->first();
            if (!$property) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found.',
                ], 404);
            }

            $demand_notice = DemandNotice::where('demand_noticeable_type', Property::class)->where('demand_noticeable_id', $property->id)->where('year_id', $requestData['year_id'])->first();
            if ($demand_notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demand Notice already exist for this year',
                ], 500);
            }
            $demand_notice = $property->demand_notices()->create($requestData);
        }
        if ($entity_prefix == 5 && $requestData['demand_notice_type'] == 'linked') {
            $signage = Signage::where('signage_id', $requestData['entity_id'])->first();
            if (!$signage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Signage not found.',
                ], 404);
            }
            $demand_notice = DemandNotice::where('demand_noticeable_type', Signage::class)->where('demand_noticeable_id', $signage->id)->where('year_id', $requestData['year_id'])->first();
            if ($demand_notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demand Notice already exist for this year',
                ], 500);
            }
            $demand_notice = $signage->demand_notices()->create($requestData);
        }
        if ($entity_prefix == 1 && $requestData['demand_notice_type'] == 'linked') {
            $individual = Individual::where('individual_id', $requestData['entity_id'])->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual not found.',
                ], 404);
            }

            $demand_notice = DemandNotice::where('demand_noticeable_type', Individual::class)->where('demand_noticeable_id', $individual->id)->where('year_id', $requestData['year_id'])->first();
            if ($demand_notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demand Notice already exist for this year',
                ], 500);
            }
            $demand_notice = $individual->demand_notices()->create($requestData);
        }

        if ($entity_prefix == 2 && $requestData['demand_notice_type'] == 'linked') {
            $cooperate = Cooperate::where('cooperate_id', $requestData['entity_id'])->first();
            if (!$cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate not found.',
                ], 404);
            }
            $demand_notice = DemandNotice::where('demand_noticeable_type', Cooperate::class)->where('demand_noticeable_id', $cooperate->id)->where('year_id', $requestData['year_id'])->first();
            if ($demand_notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demand Notice already exist for this year',
                ], 500);
            }
            $demand_notice = $cooperate->demand_notices()->create($requestData);
        }

        if ($entity_prefix == 8 && $requestData['demand_notice_type'] == 'linked') {
            $mast = Mast::where('mast_id', $requestData['entity_id'])->first();
            if (!$mast) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mast not found.',
                ], 404);
            }
            $demand_notice = DemandNotice::where('demand_noticeable_type', Mast::class)->where('demand_noticeable_id', $mast->id)->where('year_id', $requestData['year_id'])->first();
            if ($demand_notice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Demand Notice already exist for this year',
                ], 500);
            }
            $demand_notice = $mast->demand_notices()->create($requestData);
        }
        if (isset($requestData['quantity']) && $requestData['demand_notice_type'] == 'blank' && $requestData['quantity']) {
            try {
                for ($i = 0; $i < $requestData['quantity']; $i++) {
                    $requestData['demand_notice_number'] = 'DN-' . date('Y') . '-' . date('md') . '-' . rand(1000, 9999);
                    $demand_notice = DemandNotice::create($requestData);
                    $demand_notice->demand_notice_number = $requestData['demand_notice_number'] . $demand_notice->id;
                    $demand_notice->save();
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
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Demand notice has been successfully generated',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
            }
        }
        $demand_notice_category_items = DemandNoticeCategoryItem::where('demand_notice_category_id', $requestData['demand_notice_category_id'])->get();
        $demand_notice->demand_notice_number = $requestData['demand_notice_number'] . $demand_notice->id;
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
        //$requestData['status'] = 'served';
        $demand_notice_enforcement_duration = DemandNoticeCategory::findOrFail($demand_notice->demand_notice_category_id)->enforcement_duration;
        $requestData['enforcement_begins_at'] = date('Y-m-d', strtotime($requestData['date_served'] . ' + ' . $demand_notice_enforcement_duration . ' days'));
        if (!$demand_notice->has_been_served) {
            $demand_notice->update($requestData);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Demand notice has already been served.',
            ], 400);
        }
        $entity_prefix = 0;
        //if demand notice type is not set set it to none
        if (!isset($requestData['demand_notice_type'])) {
            $requestData['demand_notice_type'] = 'none';
        }
        if (isset($requestData['entity_id'])) {
            $entity_prefix = $requestData['entity_id'][0];
        }

        if ($entity_prefix == 3 && $requestData['demand_notice_type'] == 'linked') {
            $shop = Shop::where('shop_id', $requestData['entity_id'])->first();
            if (!$shop) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shop not found',
                ], 404);
            }
            $demand_notice->update([
                'demand_noticeable_type' => Shop::class,
                'demand_noticeable_id' => $shop->id
            ]);
        }
        if ($entity_prefix == 6 && $requestData['demand_notice_type'] == 'linked') {
            $vehicle = CommercialVehicle::where('vehicle_id', $requestData['entity_id'])->first();
            if (!$vehicle) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Commercial Vehicle not found.',
                ], 404);
            }
            $demand_notice->update([
                'demand_noticeable_type' => CommercialVehicle::class,
                'demand_noticeable_id' => $vehicle->id
            ]);
        }
        if ($entity_prefix == 4 && $requestData['demand_notice_type'] == 'linked') {
            $property = Property::where('property_id', $requestData['entity_id'])->first();
            if (!$property) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found.',
                ], 404);
            }
            $demand_notice->update([
                'demand_noticeable_type' => Property::class,
                'demand_noticeable_id' => $property->id
            ]);
        }
        if ($entity_prefix == 5 && $requestData['demand_notice_type'] == 'linked') {
            $signage = Signage::where('signage_id', $requestData['entity_id'])->first();
            if (!$signage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Signage not found.',
                ], 404);
            }
            $demand_notice->update([
                'demand_noticeable_type' => Signage::class,
                'demand_noticeable_id' => $signage->id
            ]);
        }
        if ($entity_prefix == 1 && $requestData['demand_notice_type'] == 'linked') {
            $individual = Individual::where('individual_id', $requestData['entity_id'])->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual not found.',
                ], 404);
            }
            $demand_notice->update([
                'demand_noticeable_type' => Individual::class,
                'demand_noticeable_id' => $individual->id
            ]);
        }

        if ($entity_prefix == 2 && $requestData['demand_notice_type'] == 'linked') {
            $cooperate = Cooperate::where('cooperate_id', $requestData['entity_id'])->first();
            if (!$cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate not found.',
                ], 404);
            }
            $demand_notice->update([
                'demand_noticeable_type' => Cooperate::class,
                'demand_noticeable_id' => $cooperate->id
            ]);
        }
        if ($entity_prefix == 8 && $requestData['demand_notice_type'] == 'linked') {
            $mast = Mast::where('mast_id', $requestData['entity_id'])->first();
            if (!$mast) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mast not found.',
                ], 404);
            }
            $demand_notice->update([
                'demand_noticeable_type' => Mast::class,
                'demand_noticeable_id' => $mast->id
            ]);
        }

        return new DemandNoticeResource($demand_notice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $demand_notice = DemandNotice::findOrFail($id);

            // Soft delete the demand notice
            $demand_notice->status = 'cancelled';
            $demand_notice->save();
            $demand_notice->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Demand notice has been successfully deleted.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Demand notice not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the demand notice: ' . $e->getMessage(),
            ], 500);
        }
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
        return new DemandNoticeResource($demand_notice);
        // $demand_notices = DemandNotice::where('user_id', $demand_notice->user_id)->where('status', 'pending')->oldest()->get();
        // return DemandNoticeResource::collection($demand_notices);
    }
}
