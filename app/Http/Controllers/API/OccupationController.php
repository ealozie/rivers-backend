<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OccupationResource;
use App\Models\Occupation;
use Illuminate\Http\Request;

/**
 * @tags Occupations Service
 */
class OccupationController extends Controller
{
    /**
     * Return all occupations.
     */
    public function __invoke(Request $request)
    {
        $occupations = Occupation::all();
        return response()->json(['status' => 'success', 'data' => OccupationResource::collection($occupations)]);
    }
}
