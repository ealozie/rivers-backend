<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketEnforcementResource;
use App\Models\TicketEnforcement;
use App\Models\TicketVending;
use Illuminate\Http\Request;

/**
 * @tags Ticket Enforcement Compliance Service
 */
class TicketEnforcementComplianceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (!$request->has('ticket_date')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket date is required.'
            ], 500);
        }
        // if (!$request->has('ticket_category_id')) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Ticket category ID is required.'
        //     ], 500);
        // }
        $ticket_date = $request->get('ticket_date');
        //$ticket_category_id = $request->get('ticket_category_id');
        // $ticket_enforcements = TicketEnforcement::whereDate('created_at', $ticket_date)->where('status', 'failed')->get();
        //return $ticket_date;
        $ticket_enforcements = TicketEnforcement::select('phone_number', 'enforcement_source', 'plate_number', \DB::raw('MAX(id) as id'))
            ->whereDate('created_at', $ticket_date)
            ->where('status', 'failed')
            ->groupBy('phone_number', 'plate_number', 'enforcement_source')
            ->get();
        //return $ticket_enforcements;
        //check if this failed ticket is already in the ticket vending table and return tickets that does not exist
        $ticket_ids_that_does_not_exist = [];
        //return $ticket_enforcements;
        foreach ($ticket_enforcements as $enforcement) {
            if ($enforcement->enforcement_source == 'plate_number') {
                $ticket_vending = TicketVending::where('plate_number', $enforcement->plate_number)->whereDate('created_at', $ticket_date)->first();
                if ($ticket_vending) {
                    $ticket_ids_that_does_not_exist[] = [
                        'id' => $enforcement->id,
                        'status' => 'YES'
                    ];
                } else {
                    $ticket_ids_that_does_not_exist[] = [
                        'id' => $enforcement->id,
                        'status' => 'NO'
                    ];
                }
            }
            if ($enforcement->enforcement_source == 'phone_number') {
                $ticket_vending = TicketVending::where('phone_number', $enforcement->phone_number)->whereDate('created_at', $ticket_date)->first();
                if ($ticket_vending) {
                    $ticket_ids_that_does_not_exist[] = [
                        'id' => $enforcement->id,
                        'status' => 'YES'
                    ];
                } else {
                    $ticket_ids_that_does_not_exist[] = [
                        'id' => $enforcement->id,
                        'status' => 'NO'
                    ];
                }
            }
        }
        //return $ticket_ids_that_does_not_exist;
        $enforcement_data = [];
        
        if (count($ticket_ids_that_does_not_exist)) {
            foreach ($ticket_ids_that_does_not_exist as $item) {
                return $item;
                $enforcement = TicketEnforcement::where('id', $item['id'])->get();
            }
            $response_data = TicketEnforcementResource::collection($enforcement_data);
        } else {
           $response_data = []; 
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket enforcement compliance fetched successfully.',
            'data' => $response_data
        ], 200);
    }
}
