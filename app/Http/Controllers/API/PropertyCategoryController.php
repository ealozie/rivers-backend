<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyCategoryResource;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;

/**
 * @tags Property Categories Service
 */
class PropertyCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $property_categories = PropertyCategory::all();
        return response()->json(['status' => 'success', 'data' => PropertyCategoryResource::collection($property_categories)]);
    }
}
