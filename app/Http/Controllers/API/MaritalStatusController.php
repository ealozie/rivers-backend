<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaritalStatusResource;
use App\Models\MaritalStatus;
use Illuminate\Http\Request;


/**
 * @tags Marital Status Service
 */
class MaritalStatusController extends Controller
{
    /**
     * Return all marital status.
     */
    public function __invoke(Request $request)
    {
        $marital_status = MaritalStatus::all();
        return response()->json([
            'status' => 'success',
            'data' => MaritalStatusResource::collection($marital_status)
        ]);
    }
}
