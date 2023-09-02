<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassificationResource;
use App\Models\Classification;
use Illuminate\Http\Request;

/**
 * @tags Classification Service
 */
class ClassificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $classifications = Classification::all();
        return response()->json(['status' => 'success', 'data' => ClassificationResource::collection($classifications)]);
    }
}
