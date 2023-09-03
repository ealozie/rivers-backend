<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Http\Request;

/**
 * @tags States Service
 */
class StateController extends Controller
{
    /**
     * Return all states.
     */
    public function __invoke(Request $request)
    {
        $states = State::all();
        return response()->json(['status' => 'success', 'data' => StateResource::collection($states)]);
    }
}
