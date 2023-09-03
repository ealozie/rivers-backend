<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarketNameResource;
use App\Models\MarketName;
use Illuminate\Http\Request;

/**
 * @tags Market Name Service
 */
class MarketNameController extends Controller
{
    /**
     * Return all markets in state.
     */
    public function __invoke(Request $request)
    {
        $market_names = MarketName::all();
        return response()->json(['status' => 'success', 'data' => MarketNameResource::collection($market_names)]);
    }
}
