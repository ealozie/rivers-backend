<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CommercialVehicle;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Vehicle Enumeration Verification Service
 */
class VehicleEnumerationVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $requestData = $request->validate([
            'plate_number' => 'required|string',
        ]);

        //Check if user is an agent
        $agent = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $agent->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to process tickets. Contact the administrator for assistance.',
            ], 403);
        }

        $plate_number = strtoupper($requestData['plate_number']);
        $commercial_vehicle = CommercialVehicle::where('plate_number', $plate_number)->first();
        
        if (!$commercial_vehicle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plate number not found.',
                'data' => [
                    'phone_number' => null,
                    'plate_number' => $plate_number,
                    'ticket_category_id' => null,
                    'is_agent_allowed_to_vend_ticket_category' => null
                ],
            ], 404);
        }

        $user = User::find($commercial_vehicle->user_id);
        //Check if agent is allowed to vend ticket
        $ticket_agent_category = TicketAgentCategory::where('ticket_agent_id', $ticket_agent->id)->where('ticket_category_id', $commercial_vehicle->ticket_category_id)->first();
        (bool) $status = false;
        if ($ticket_agent_category) {
            $status = true;
        } else {
            $status = false;
        }
        return response()->json([
                'status' => 'success',
                'message' => 'Plate number found.',
                'data' => [
                    'phone_number' => $user->phone_number,
                    'plate_number' => $plate_number,
                    'ticket_category_id' => (int) $commercial_vehicle->ticket_category_id,
                    'is_agent_allowed_to_vend_this_ticket_category' => $status
                ],
            ], 200);
    }
}
