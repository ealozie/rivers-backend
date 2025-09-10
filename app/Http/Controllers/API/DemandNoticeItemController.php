<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandNoticeItemStoreRequest;
use App\Http\Requests\DemandNoticeItemUpdateRequest;
use App\Http\Resources\DemandNoticeItemResource;
use App\Models\DemandNoticeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @tags Demand Notice Item Service
 */
final class DemandNoticeItemController extends Controller
{
    /**
     * Display a listing of the demand notice items.
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page ? $request->per_page : 50;

        $query = DemandNoticeItem::with(['revenueItem', 'demandNotice', 'year']);

        // Filter by demand notice ID if provided
        if ($request->has('demand_notice_id')) {
            $query->where('demand_notice_id', $request->demand_notice_id);
        }

        // Filter by payment status if provided
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by year ID if provided
        if ($request->has('year_id')) {
            $query->where('year_id', $request->year_id);
        }

        $demand_notice_items = $query->paginate($per_page);

        return DemandNoticeItemResource::collection($demand_notice_items);
    }

    /**
     * Store a newly created demand notice item in storage.
     */
    public function store(DemandNoticeItemStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $demand_notice_item = DemandNoticeItem::create($request->validated());

            DB::commit();

            return new DemandNoticeItemResource($demand_notice_item->load(['revenueItem', 'demandNotice', 'year']));

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the demand notice item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified demand notice item.
     */
    public function show(string $id)
    {
        try {
            $demand_notice_item = DemandNoticeItem::with(['revenueItem', 'demandNotice', 'year'])
                ->findOrFail($id);

            return new DemandNoticeItemResource($demand_notice_item);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Demand notice item not found.',
            ], 404);
        }
    }

    /**
     * Update the specified demand notice item in storage.
     */
    public function update(DemandNoticeItemUpdateRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $demand_notice_item = DemandNoticeItem::findOrFail($id);
            $demand_notice_item->update($request->validated());

            DB::commit();

            return new DemandNoticeItemResource($demand_notice_item->load(['revenueItem', 'demandNotice', 'year']));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Demand notice item not found.',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the demand notice item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified demand notice item from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $demand_notice_item = DemandNoticeItem::findOrFail($id);
            $demand_notice_item->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Demand notice item has been successfully deleted.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Demand notice item not found.',
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the demand notice item: ' . $e->getMessage(),
            ], 500);
        }
    }
}
