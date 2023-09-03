<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettlementTypeResource;
use App\Models\SettlementType;
use Filament\Forms\Set;
use Illuminate\Http\Request;

/**
 * @tags Settlement Type Service
 */
class SettlementTypeController extends Controller
{
    /**
     * Return all settlement types.
     */
    public function __invoke(Request $request)
    {
        $settlement_types = SettlementType::all();
        return response()->json(['status' => 'success', 'data' => SettlementTypeResource::collection($settlement_types)]);
    }
}
