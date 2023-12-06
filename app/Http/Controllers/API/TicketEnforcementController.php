<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketEnforcementResource;
use App\Http\Resources\TicketVendingResource;
use App\Models\TicketAgent;
use App\Models\TicketEnforcement;
use App\Models\TicketVending;
use Axiom\Rules\LocationCoordinates;
use Illuminate\Http\Request;

/**
 * @tags Ticket Enforcement Service
 */
class TicketEnforcementController extends Controller
{
    /**
     * Return all enforcements.
     *
     * Query paramters `plate_number` or `ticket_category_id`.<br>
     * Additonal Query paramters `limit` and `offset`
     */
    public function index(Request $request)
    {

        $user = $request->user();

        $limit = 10;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = (int) $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = (int) $request->get('offset');
        }

        if ($user->hasRole('admin')) {
            $ticket_enforcements = TicketEnforcement::latest()->offset($offset)->limit($limit)->get();
            $total_number_of_records = TicketEnforcement::count();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'ticket_enforcement_data' => TicketEnforcementResource::collection($ticket_enforcements),
                    'total_number_of_records' => (int) $total_number_of_records
                ]
            ]);
        }

        if ($user->hasRole('super_agent')) {
            $sub_agents = TicketAgent::where('super_agent_id', $request->user()->id)->pluck('id')->toArray();
            $ticket_enforcements = TicketEnforcement::whereIn('ticket_agent_id', $sub_agents)->latest()->offset($offset)->limit($limit)->get();
            $total_number_of_records = TicketEnforcement::whereIn('ticket_agent_id', $sub_agents)->count();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'ticket_enforcement_data' => TicketEnforcementResource::collection($ticket_enforcements),
                    'total_number_of_records' => (int) $total_number_of_records
                ]
            ]);
        }

        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();

        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to process tickets. Contact the administrator for assistance.',
            ], 403);
        }

        $ticket_enforcements = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->latest()->offset($offset)->limit($limit)->get();
        $total_number_of_records = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->count();

        if ($request->has('plate_number')) {
            $plate_number = $request->get('plate_number');
            $ticket_enforcements = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->where('plate_number', $plate_number)->latest()->offset($offset)->limit($limit)->get();
            $total_number_of_records = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->where('plate_number', $plate_number)->count();
        }

        if ($request->has('ticket_category_id')) {
            $ticket_category_id = $request->get('ticket_category_id');
            $ticket_enforcements = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->where('ticket_category_id', $ticket_category_id)->latest()->offset($offset)->limit($limit)->get();
            $total_number_of_records = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->where('ticket_category_id', $ticket_category_id)->count();
        }

        if ($request->has('plate_number') && $request->has('ticket_category_id')) {
            $plate_number = $request->get('plate_number');
            $ticket_category_id = $request->get('ticket_category_id');
            $ticket_enforcements = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->where('plate_number', $plate_number)->where('ticket_category_id', $ticket_category_id)->latest()->offset($offset)->limit($limit)->get();
            $total_number_of_records = TicketEnforcement::where('ticket_agent_id', $ticket_agent->id)->where('plate_number', $plate_number)->where('ticket_category_id', $ticket_category_id)->count();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_enforcement_data' => TicketEnforcementResource::collection($ticket_enforcements),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
    }

    /**
     * Get ticket agent enforcements.
     */
    public function ticket_agent_enforcements($agent_id)
    {
        $ticket_enforcements = TicketEnforcement::where('ticket_agent_id', $agent_id)->latest()->get();
        return TicketEnforcementResource::collection($ticket_enforcements);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * `geo_location_coordinates` is optional but if provided. The given value should be a comma-separated set of latitude and longitude coordinates. Example `4.6604761,7.9411649`.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'plate_number' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'enforcement_source' => 'required|in:plate_number,phone_number',
            'ticket_category_id' => 'sometimes|integer',
            'geo_location_coordinates' => ['sometimes', new LocationCoordinates],
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
        if ($request->enforcement_source == 'plate_number' && isset($validatedData['plate_number'])) {
            $ticket_vending = TicketVending::ofToday()->where('plate_number', $validatedData['plate_number'])->latest()->get();
        } else if ($request->enforcement_source == 'phone_number' && isset($validatedData['phone_number'])) {
            $ticket_vending = TicketVending::ofToday()->where('phone_number', $validatedData['phone_number'])->latest()->get();
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request',
            ], 500);
        }

        // if ($request->has('ticket_category_id') && $request->has('phone_number')) {
        //     $ticket_vending = TicketVending::ofToday()->where('phone_number', $validatedData['phone_number'])->where('ticket_category_id', $validatedData['ticket_category_id'])->latest()->get();
        // }

        // if ($request->has('ticket_category_id') && $request->has('plate_number')) {
        //     $ticket_vending = TicketVending::ofToday()->where('plate_number', $validatedData['plate_number'])->where('ticket_category_id', $validatedData['ticket_category_id'])->latest()->get();
        // }

        $status = 'failed';
        if (count($ticket_vending)) {
            $status = 'success';
        }
        try {
            $ticket_enforcement = new TicketEnforcement();
            $ticket_enforcement->plate_number = $request->plate_number;
            $ticket_enforcement->phone_number = $request->phone_number;
            $ticket_enforcement->ticket_agent_id = $ticket_agent->id;
            $ticket_enforcement->enforcement_source = $request->enforcement_source;
            if (isset($request->geo_location_coordinates)) {
                $coodinates = explode(",",$request->geo_location_coordinates);
                $ticket_enforcement->latitude = $coodinates[0];
                $ticket_enforcement->longitude = $coodinates[1];
            }
            //$ticket_enforcement->ticket_category_id = $validatedData['ticket_category_id'] ?? 0;
            $ticket_enforcement->ticket_category_id = 0;
            $ticket_enforcement->response = count($ticket_vending) ? json_encode(TicketVendingResource::collection($ticket_vending)) : json_encode($ticket_vending);
            $ticket_enforcement->status = $status;
            $ticket_enforcement->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving ticket enforcement.',
                'error' => $e->getMessage()
            ], 500);
        }

        if ($request->has('plate_number')) {
            $total_enforcements_today = TicketEnforcement::ofToday()->where('plate_number', $validatedData['plate_number'])->count();
        }

        if ($request->has('phone_number')) {
            $total_enforcements_today = TicketEnforcement::ofToday()->where('phone_number', $validatedData['phone_number'])->count();
        }

        if ($status == 'failed') {
            return response()->json([
                'status' => 'error',
                'message' => 'No ticket found.',
                'total_enforcements_today' => $total_enforcements_today,
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Tickets retrieved successfully',
            'total_enforcements_today' => $total_enforcements_today,
            'data' => TicketVendingResource::collection($ticket_vending),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `ticket_category_id` or `plate_number`.<br>
     * Additonal Query paramters `ticket_agent_id`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $per_page = 20;
        
        if ($request->has('plate_number')) {
            $query_request = $request->get('plate_number');
            $ticket_response = TicketEnforcement::where('plate_number', $query_request)->paginate($per_page);
        }
        if ($request->has('ticket_category_id')) {
            $query_request = $request->get('ticket_category_id');
            $ticket_response = TicketEnforcement::where('ticket_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('ticket_agent_id')) {
            $query_request = $request->get('ticket_agent_id');
            $ticket_response = TicketEnforcement::where('ticket_agent_id', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $ticket_response = TicketEnforcement::whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($ticket_response)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return TicketEnforcementResource::collection($ticket_response);
    }
}
