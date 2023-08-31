<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentCategoryResource;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use Illuminate\Http\Request;

/**
 * @tags Ticket Agent Categories Service
 */

class TicketAgentCategoryController extends Controller
{
    /**
     * Get all Ticket Agent Categories.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to enforce tickets. Contact the administrator for assistance.',
            ], 403);
        }
        $agent_categories = TicketAgentCategory::where('ticket_agent_id', $ticket_agent->id)->get();
        return response()->json([
            'status' => 'success',
            'data' => TicketAgentCategoryResource::collection($agent_categories),
        ], 200);
    }
}
