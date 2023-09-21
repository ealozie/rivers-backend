<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @tags Ticket Agent Status Service
 */
class TicketAgentStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     */
    public function __invoke(Request $request)
    {
        $agent_status = ['active', 'inactive'];
        return response()->json(['status' => 'success', 'data' => $agent_status]);
    }
}
