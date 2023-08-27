<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketAgentCategoryResource;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use Illuminate\Http\Request;

/**
 * @tags Ticket Agent Category Service
 */
class TicketAgentCategoryController extends Controller
{
    /**
     * Display listing of all resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return all list of ticket categories that been assigned to current authenticated agent.
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
