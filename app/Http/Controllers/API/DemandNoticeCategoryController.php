<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DemandNoticeCategoryResource;
use App\Models\DemandNoticeCategory;
use Illuminate\Http\Request;

/**
 * @tags Demand Notice Category Service
 */
class DemandNoticeCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $demand_notice_categories = DemandNoticeCategory::all();
        return response()->json(['status' => 'success', 'data' => DemandNoticeCategoryResource::collection($demand_notice_categories)]);
    }
}
