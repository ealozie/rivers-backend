<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessTypeResource;
use App\Models\BusinessType;
use Illuminate\Http\Request;

/**
 * @tags Business Type Service
 */
class BusinessTypeController extends Controller
{
    /**
     * Return all businesses type.
     */
    public function __invoke(Request $request)
    {
        $business_types = BusinessType::all();
        return response()->json(['status' => 'success', 'data' => BusinessTypeResource::collection($business_types)]);
    }
}
