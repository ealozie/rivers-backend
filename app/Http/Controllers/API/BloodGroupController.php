<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BloodGroupResource;
use App\Models\BloodGroup;
use Illuminate\Http\Request;

/**
 * @tags Blood Group Service
 */
class BloodGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $blood_groups = BloodGroup::all();
        return response()->json([
            'status' => 'success',
            'data' => BloodGroupResource::collection($blood_groups)
        ]);
    }
}
