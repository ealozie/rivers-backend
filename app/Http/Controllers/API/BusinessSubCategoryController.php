<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessSubCategoryResource;
use App\Models\BusinessSubCategory;
use Illuminate\Http\Request;

/**
 * @tags Business Sub Category Service
 */
class BusinessSubCategoryController extends Controller
{
    /**
     * Return all business sub categories.
     */
    public function __invoke(Request $request, $business_category_id)
    {
        $business_sub_categories = BusinessSubCategory::where('business_category_id', $business_category_id)->get();
        return response()->json(['status' => 'success', 'data' => BusinessSubCategoryResource::collection($business_sub_categories)]);
    }
}
