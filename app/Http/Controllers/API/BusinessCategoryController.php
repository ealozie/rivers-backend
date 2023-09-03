<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessCategoryResource;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;

/**
 * @tags Business Category Service
 */
class BusinessCategoryController extends Controller
{
    /**
     * Return all business category.
     */
    public function __invoke(Request $request)
    {
        $business_categories = BusinessCategory::all();
        return response()->json(['status' => 'success', 'data' => BusinessCategoryResource::collection($business_categories)]);
    }
}
