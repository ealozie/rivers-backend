<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @tags Ticket Agent Types Service
 */
class TicketAgentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     */
    public function __invoke(Request $request)
    {
        $agent_types = ['sales', 'enforcer'];
        return response()->json(['status' => 'success', 'data' => $agent_types]);
    }
}
