<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessLevelResource;
use App\Models\BusinessLevel;
use Illuminate\Http\Request;

/**
 * @tags Business Level Service
 */
class BusinessLevelController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $business_levels = BusinessLevel::all();
        return response()->json(['status' => 'success', 'data' => BusinessLevelResource::collection($business_levels)]);
    }
}
