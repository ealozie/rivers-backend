<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RevenueTypeResource;
use App\Models\RevenueType;
use Illuminate\Http\Request;

/**
 * @tags Revenue Type Service
 */
class RevenueTypeController extends Controller
{
    /**
     * Return all revenue types.
     */
    public function __invoke(Request $request)
    {
        $revenue_types = RevenueType::all();
        return response()->json(['status' => 'success', 'data' => RevenueTypeResource::collection($revenue_types)]);
    }
}
