<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NationalityResource;
use App\Models\Nationality;
use Illuminate\Http\Request;

/**
 * @tags Nationality Service
 */
class NationalityController extends Controller
{
    /**
     * Return all nationalities.
     */
    public function __invoke(Request $request)
    {
        $nationalities = Nationality::all();
        return response()->json(['status' => 'success', 'data' => NationalityResource::collection($nationalities)]);
    }
}
