<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketBulkVendingCollection;
use App\Http\Resources\TicketBulkVendingResource;
use App\Models\CommercialVehicle;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use App\Models\TicketAgentWallet;
use App\Models\TicketBulkVending;
use App\Models\TicketCategory;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

/**
 * @tags Ticket Bulk Vending Service
 */
class TicketBulkVendingController extends Controller
{
    use SendSMS;
    /**
     * Get all resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return list of all ticket bulk vending that been vended by current authenticated agent. <br>
     * Additional Query parameter are `limit` and `offset`. <br>
     * Additional Query parameter `?query=all`, return all resources
     * without pagination.
     */
    public function index(Request $request)
    {
        $per_page = 20;
        $limit = 10;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }
        $ticket_bulk_vending = TicketBulkVending::where('user_id', $request->user()->id)->latest()->offset($offset)->limit($limit)->get();
        $total_number_of_records = TicketBulkVending::where('user_id', $request->user()->id)->count();

        //$ticket_bulk_vending = TicketBulkVending::where('user_id', $request->user()->id)->latest()->paginate($per_page);

        $user = $request->user();
        
        if ($user->hasRole('admin')) {
            $ticket_bulk_vending = TicketBulkVending::latest()->paginate($per_page);
            if ($request->has('query') && $request->get('query') == 'all') {
                $ticket_bulk_vending = TicketBulkVending::latest()->get();
            }
        }

        if (!count($ticket_bulk_vending)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                'ticket_bulk_vending_data' => [],
                'total_number_of_records' => (int) $total_number_of_records
            ],
            ], 200);
        }
       // return $ticket_bulk_vending;
        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_bulk_vending_data' => TicketBulkVendingResource::collection($ticket_bulk_vending),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
        // if (!count($ticket_bulk_vending)) {
        //     return [];
        // }
        // $ticket_bulk_collection = new TicketBulkVendingCollection($ticket_bulk_vending);
        // return $ticket_bulk_collection;
    }

    /**
     * Store a resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'plate_number' => 'required|string',
            'phone_number' => 'required|string',
            'ticket_category_id' => 'sometimes|required|integer',
            'number_of_tickets' => 'required|integer|min:1'
        ]);
        $plate_number = strtoupper($validatedData['plate_number']);
        $phone_number = $validatedData['phone_number'];

        $commercial_vehicle = CommercialVehicle::where('plate_number', $plate_number)->first();
        if (!$commercial_vehicle && !$request->has('ticket_category_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plate number not found. Select a ticket category to continue.',
            ], 404);
        }
        $response_data = [];
        $ticket_category_id = $validatedData['ticket_category_id'];
        $response_data['category_id'] = $ticket_category_id;
        $is_enumerated = false;
        if ($commercial_vehicle) {
            $response_data['category_id'] = $commercial_vehicle->category_id;
            $is_enumerated = true;
        }

        //Check if user is an agent
        $user = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to process tickets. Contact the administrator for assistance.',
            ], 403);
        }

        //Check if agent is allowed to vend ticket
        $ticket_agent_category = TicketAgentCategory::where('ticket_agent_id', $ticket_agent->id)->where('ticket_category_id', $response_data['category_id'])->first();
        if (!$ticket_agent_category) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to vend this ticket category.',
            ], 403);
        }

        //Check if category accept multiple and if agent has purchased for that day.
        $ticket_category = TicketCategory::find($response_data['category_id']);
        if ((int)$ticket_category->allow_multiple_ticket_purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to vend a ticket category that support multiple purchase.',
            ], 403);
        }

        //Check if agent wallet balance is enough to vend that number of tickets
        $number_of_tickets = $validatedData['number_of_tickets'];
        $total_amount = $ticket_category->amount * $number_of_tickets;
        if ($ticket_agent->wallet_balance < $total_amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have enough balance to vend this number of tickets.',
                'data' => [
                    'number_of_tickets' => $validatedData['number_of_tickets'],
                    'total_amount' => $total_amount,
                    'wallet_balance' => $ticket_agent->wallet_balance
                ]
            ], 403);
        }

        try {
            //Check if agent has discount for this ticket then calculate the ticket price
            $ticket_price = $ticket_category->amount;
            $ticket_actual_price = $ticket_category->amount;
            if ($ticket_agent->discount) {
                $price = $ticket_price - ($ticket_price * $ticket_agent->discount / 100);
                $ticket_price = round($price);
            }
            $ticket_bulk_vending = new TicketBulkVending();
            $ticket_bulk_vending->plate_number = $plate_number;
            $ticket_bulk_vending->ticket_category_id = $response_data['category_id'];
            $ticket_bulk_vending->amount = $ticket_price;
            $ticket_bulk_vending->ticket_amount = $ticket_actual_price;
            $ticket_bulk_vending->agent_discount = $ticket_agent->discount;
            $ticket_bulk_vending->ticket_agent_id = $ticket_agent->id;
            $ticket_bulk_vending->user_id = $user->id;
            $ticket_bulk_vending->remaining_tickets = $number_of_tickets;
            $ticket_bulk_vending->total_tickets = $number_of_tickets;
            $ticket_bulk_vending->expired_at = $ticket_category->expired_at;
            $ticket_bulk_vending->status = 'active';
            $ticket_bulk_vending->save();
            //Deduct agent wallet balance
            $ticket_agent->wallet_balance = $ticket_agent->wallet_balance - $ticket_price * $number_of_tickets;
            $ticket_agent->save();

            //Add to agent wallet transaction
            $ticket_agent_wallet = new TicketAgentWallet();
            $ticket_agent_wallet->ticket_agent_id = $ticket_agent->id;
            $ticket_agent_wallet->user_id = $user->id;
            $ticket_agent_wallet->amount = $ticket_price;
            $ticket_agent_wallet->transaction_type = 'debit';
            $ticket_agent_wallet->type = 'bulk_tickets';
            $ticket_agent_wallet->transaction_status = 'active';
            $ticket_agent_wallet->added_by = $user->id;
            $ticket_agent_wallet->transaction_reference_number = date('isYd');
            $ticket_agent_wallet->save();
            //Send SMS to user
            $mobile_number = ltrim($phone_number, "0");
            $message = "Your ticket has been successfully processed. A total of " . $number_of_tickets . " ticket(s) has been processed for " . $plate_number . " for the next {$number_of_tickets} day(s). Thank you.";
            $this->send_sms_process_message("+234" . $mobile_number, $message);
            return response()->json([
                'status' => 'success',
                'message' => 'Bulk Ticket(s) purchased successfully.',
                'data' => [
                    'is_enumerated' => $is_enumerated,
                    'ticket' => new TicketBulkVendingResource($ticket_bulk_vending),
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'data' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     */
    public function show(string $id)
    {
        $ticket_bulk_vending = TicketBulkVending::find($id);
        if (!$ticket_bulk_vending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket vending not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket vending retrieved successfully.',
            'data' => new TicketBulkVendingResource($ticket_bulk_vending),
        ], 200);
    }

    /**
     * Get all tickets by agent ID resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return list of all ticket bulk vending that has been initiated by agent. <br>
     * Additional Query parameter are `limit` and `offset`. <br>
     * Additional Query parameter `?query=all`, return all resources
     * without pagination.
     */
    public function tickets_by_agent(Request $request, $ticket_agent_id)
    {
        $per_page = 20;
        $limit = 10;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }
        $ticket_bulk_vending = TicketBulkVending::where('ticket_agent_id', $ticket_agent_id)->latest()->offset($offset)->limit($limit)->get();
        $total_number_of_records = TicketBulkVending::where('ticket_agent_id', $ticket_agent_id)->count();

        if ($request->has('query') && $request->get('query') == 'all') {
            $ticket_bulk_vending = TicketBulkVending::where('ticket_agent_id', $ticket_agent_id)->latest()->get();
        }

        if (!count($ticket_bulk_vending)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                'ticket_bulk_vending_data' => [],
                'total_number_of_records' => (int) $total_number_of_records
            ],
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_bulk_vending_data' => new TicketBulkVendingCollection($ticket_bulk_vending),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `plate_number` or `ticket_category_id`.<br>
     * Additonal Query paramters `ticket_agent_id`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $per_page = 20;
        
        if ($request->has('plate_number')) {
            $query_request = $request->get('plate_number');

            $ticket_response = TicketBulkVending::where('plate_number', $query_request)->paginate($per_page);
        }
        if ($request->has('ticket_category_id')) {
            $query_request = $request->get('ticket_category_id');
            $ticket_response = TicketBulkVending::where('ticket_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('ticket_agent_id')) {
            $query_request = $request->get('ticket_agent_id');
            $ticket_response = TicketBulkVending::where('ticket_agent_id', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $ticket_response = TicketBulkVending::whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($ticket_response)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }

        return TicketBulkVendingResource::collection($ticket_response);
    }
}
