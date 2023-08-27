<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketVendingResource;
use App\Models\TicketAgent;
use App\Models\TicketEnforcement;
use App\Models\TicketVending;
use Illuminate\Http\Request;

/**
 * @tags Ticket Enforcement Service
 */
class TicketEnforcementController extends Controller
{
    /**
     * Initiate the enforcement request.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'plate_number' => 'required|string',
            'ticket_category_id' => 'sometimes|integer'
        ]);
        $user = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to enforce tickets. Contact the administrator for assistance.',
            ], 403);
        }
        //Get records from TicketVending by plate number
        $ticket_vending = TicketVending::ofToday()->where('plate_number', $validatedData['plate_number'])->latest()->get();
        if ($request->has('ticket_category_id')) {
            $ticket_vending = TicketVending::ofToday()->where('plate_number', $validatedData['plate_number'])->where('ticket_category_id', $validatedData['ticket_category_id'])->latest()->get();
        }
        $status = 'failed';
        if (count($ticket_vending)) {
            $status = 'success';
        }
        try {
            $ticket_enforcement = new TicketEnforcement();
            $ticket_enforcement->plate_number = $validatedData['plate_number'];
            $ticket_enforcement->ticket_agent_id = $ticket_agent->id;
            $ticket_enforcement->ticket_category_id = $validatedData['ticket_category_id'] ?? 0;
            $ticket_enforcement->response = json_encode($ticket_vending);
            $ticket_enforcement->status = $status;
            $ticket_enforcement->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving ticket enforcement.',
                'error' => $e->getMessage()
            ], 500);
        }
        if ($status == 'failed') {
            return response()->json([
                'status' => 'error',
                'message' => 'No ticket found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Tickets retrieved successfully',
            'data' => TicketVendingResource::collection($ticket_vending),
        ]);
    }
}
