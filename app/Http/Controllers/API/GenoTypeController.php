<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenoTypeResource;
use App\Models\GenoType;
use Illuminate\Http\Request;

/**
 * @tags Geno Type Service
 */
class GenoTypeController extends Controller
{
    /**
     * Return all geno types.
     */
    public function __invoke(Request $request)
    {
        $geno_types = GenoType::all();
        return response()->json([
            'status' => 'success',
            'data' => GenoTypeResource::collection($geno_types)
        ]);
    }
}
