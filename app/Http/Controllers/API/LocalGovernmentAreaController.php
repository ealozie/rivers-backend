<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocalGovernmentAreaResource;
use App\Models\LocalGovernmentArea;
use Illuminate\Http\Request;

/**
 * @tags Local Government Area Service
 */
class LocalGovernmentAreaController extends Controller
{
    /**
     * Return all Government Areas by its state ID.
     */
    public function __invoke(Request $request, $state_id)
    {
        $local_government_areas = LocalGovernmentArea::where('state_id', $state_id)->get();
        return response()->json(['status' => 'success', 'data' => LocalGovernmentAreaResource::collection($local_government_areas)]);
    }
}
