<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketEnforcementComplianceResource;
use App\Http\Resources\TicketEnforcementResource;
use App\Http\Resources\TicketVendingResource;
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
                        'status' => 'YES',
                        'enforcement_source' => 'plate_number',
                    ];
                } else {
                    $ticket_ids_that_does_not_exist[] = [
                        'id' => $enforcement->id,
                        'status' => 'NO',
                        'enforcement_source' => 'plate_number',
                    ];
                }
            }
            if ($enforcement->enforcement_source == 'phone_number') {
                $ticket_vending = TicketVending::where('phone_number', $enforcement->phone_number)->whereDate('created_at', $ticket_date)->first();
                if ($ticket_vending) {
                    $ticket_ids_that_does_not_exist[] = [
                        'id' => $enforcement->id,
                        'status' => 'YES',
                        'enforcement_source' => 'phone_number',
                    ];
                } else {
                    $ticket_ids_that_does_not_exist[] = [
                        'id' => $enforcement->id,
                        'status' => 'NO',
                        'enforcement_source' => 'phone_number',
                    ];
                }
            }
        }
        //return $ticket_ids_that_does_not_exist;
        $enforcement_data = [];
        if (count($ticket_ids_that_does_not_exist)) {
            foreach ($ticket_ids_that_does_not_exist as $item) {
                //return $item;
                $enforcement = TicketEnforcement::where('id', $item['id'])->first();
                //return $enforcement;
                $enforcement->vending_status = $item['status'];
                if ($enforcement->enforcement_source == 'phone_number') {
                    $ticket_vendings = TicketVending::where('phone_number', $enforcement->phone_number)->whereDate('created_at', $ticket_date)->get();
                }
                if ($enforcement->enforcement_source == 'plate_number') {
                    $ticket_vendings = TicketVending::where('plate_number', $enforcement->plate_number)->whereDate('created_at', $ticket_date)->get();
                }
                $enforcement->ticket_vendings = TicketVendingResource::collection($ticket_vendings);
                $enforcement_data[] = $enforcement;
            }
            $response_data = TicketEnforcementComplianceResource::collection($enforcement_data);
        } else {
           $response_data = []; 
        }
        //return $response_data;
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket enforcement compliance fetched successfully.',
            'data' => $response_data
        ], 200);
    }
}
