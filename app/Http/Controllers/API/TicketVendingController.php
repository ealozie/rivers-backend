<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketVendingCollection;
use App\Http\Resources\TicketVendingResource;
use App\Models\CommercialVehicle;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use App\Models\TicketAgentWallet;
use App\Models\TicketCategory;
use App\Models\TicketVending;
use App\Models\User;
use App\Traits\SendSMS;
use Axiom\Rules\LocationCoordinates;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @tags Ticket Vending Service
 */
class TicketVendingController extends Controller
{
    use SendSMS;
    /**
     * Get all resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return list of all ticket vending that been vended by current authenticated agent. <br>
     * Additional Query parameter are `limit` and `offset`. <br>
     * Additional Query parameter `?query=all`, return all resources
     * without pagination.
     */
    public function index(Request $request)
    {
        $limit = 10;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }
        $ticket_vending = TicketVending::where('user_id', $request->user()->id)->latest()->offset($offset)->limit($limit)->get();
        $total_number_of_records = TicketVending::where('user_id', $request->user()->id)->count();
        $user = $request->user();
        if ($user->hasRole('admin')) {
            $ticket_vending = TicketVending::latest()->offset($offset)->limit($limit)->get();
            if ($request->has('query') && $request->get('query') == 'all') {
                $ticket_vending = TicketVending::latest()->get();
            }
            $total_number_of_records = TicketVending::count();
        }
        if ($user->hasRole('super_agent')) {
            $sub_agents = TicketAgent::where('super_agent_id', $request->user()->id)->orWhere('user_id', $request->user()->id)->pluck('user_id')->toArray();
            $ticket_vending = TicketVending::latest()->offset($offset)->limit($limit)->whereIn('user_id', $sub_agents)->get();
            if ($request->has('query') && $request->get('query') == 'all') {
                $ticket_vending = TicketVending::whereIn('user_id', $sub_agents)->latest()->get();
            }
            $total_number_of_records = TicketVending::whereIn('user_id', $sub_agents)->count();
        }

        if (!count($ticket_vending)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'ticket_vending_data' => [],
                    'total_number_of_records' => (int) $total_number_of_records
                ],
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_vending_data' => TicketVendingResource::collection($ticket_vending),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
    }

    /**
     * Store a resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br> And only ticket agent can vend ticket.
     * 
     * `geo_location_coordinates` is optional but if provided. The given value should be a comma-separated set of latitude and longitude coordinates. Example `4.6604761,7.9411649`.
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'plate_number' => 'required|string',
            'phone_number' => 'required|string',
            'ticket_category_id' => 'required|string',
            'owner_name' => 'required|string',
            'quantity' => 'sometimes|numeric',
            'geo_location_coordinates' => ['sometimes', new LocationCoordinates],
        ]);
        if (isset($requestData['quantity'])) {
            $quantity = $requestData['quantity'];
        } else {
           $quantity = 1; 
        }
        $phone_number = $requestData['phone_number'];
        $ticket_category_id = $requestData['ticket_category_id'];
        $plate_number = $requestData['plate_number'];

        //Check if user is an agent
        $user = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to process tickets. Contact the administrator for assistance.',
            ], 403);
        }

        if ($ticket_agent->agent_status != 'active' ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account has been placed on hold.'
            ], 401);
        }

        //Check if category accept multiple and if agent has purchased for that day.
        $ticket_category = TicketCategory::find($ticket_category_id);
        $category_name = $ticket_category->category_name;
        //Check if agent is allowed to vend ticket
        $ticket_agent_category = TicketAgentCategory::where('ticket_agent_id', $ticket_agent->id)->where('ticket_category_id', $ticket_category_id)->first();
        if (!$ticket_agent_category) {
            return response()->json([
                'status' => 'error',
                'message' => "You cannot vend for {$category_name} ticket  category.",
            ], 403);
        }



        if (!$ticket_category->allow_multiple_ticket_purchase) {
            $ticket_vending = TicketVending::where('plate_number', $plate_number)->where('ticket_category_id', $ticket_category_id)->whereDate('created_at', date('Y-m-d'))->first();
            if ($ticket_vending) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already purchased ticket for this category today.',
                    "data" => new TicketVendingResource($ticket_vending),
                ], 403);
            }
        }

        //Check if agent has enough balance
        //Check if agent has discount for this ticket then calculate the ticket price
        $ticket_price = $ticket_category->amount * $quantity;
        $ticket_actual_price = $ticket_category->amount * $quantity;
        if ($ticket_agent->discount) {
            $price = $ticket_price - ($ticket_price * $ticket_agent->discount / 100);
            $ticket_price = round($price) * $quantity;
        }

        if ($ticket_agent->wallet_balance < $ticket_price) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have enough balance to purchase this ticket.',
                'data' => [
                    'wallet_balance' => number_format($ticket_agent->wallet_balance, 2),
                    'ticket_price_before_discount' => number_format($ticket_actual_price, 2),
                    'discount' => $ticket_agent->discount . '%',
                    'ticket_price_after_discount' => number_format($ticket_price, 2),
                ]
            ], 403);
        }

        //Proceed to vend or create a record for this
        try {
            $ticket_vending = new TicketVending();
            $ticket_vending->plate_number = $plate_number;
            $ticket_vending->ticket_category_id = $ticket_category_id;
            $ticket_vending->amount = $ticket_price;
            $ticket_vending->quantity = $quantity;
            $ticket_vending->discounted_price = $ticket_price;
            $ticket_vending->owner_name = $requestData['owner_name'];
            $ticket_vending->ticket_amount = $ticket_actual_price;
            $ticket_vending->agent_discount = $ticket_agent->discount;
            $ticket_vending->ticket_agent_id = $ticket_agent->id;
            $ticket_vending->user_id = $user->id;
            $ticket_vending->phone_number = $phone_number;
            if (isset($request->geo_location_coordinates)) {
                $coodinates = explode(",",$request->geo_location_coordinates);
                $ticket_vending->latitude = $coodinates[0];
                $ticket_vending->longitude = $coodinates[1];
            }
            
            $ticket_vending->expired_at = $ticket_category->expired_at;
            $ticket_vending->ticket_status = 'active';
            $ticket_vending->ticket_reference_number = date('isYd');
            $ticket_vending->save();
            //Debit wallet balance and log the transaction
            $ticket_agent->wallet_balance = $ticket_agent->wallet_balance - $ticket_price;
            $ticket_agent->save();
            //Log transaction
            $ticket_agent_wallet = new TicketAgentWallet();
            $ticket_agent_wallet->ticket_agent_id = $ticket_agent->id;
            $ticket_agent_wallet->user_id = $user->id;
            $ticket_agent_wallet->amount = $ticket_price;
            $ticket_agent_wallet->transaction_type = 'debit';
            $ticket_agent_wallet->transaction_status = 'active';
            $ticket_agent_wallet->added_by = $user->id;
            $ticket_agent_wallet->transaction_reference_number = $ticket_vending->ticket_reference_number;
            $ticket_agent_wallet->save();
            //Send SMS to user
            $mobile_number = ltrim($phone_number, "0");
            $ticket_category_name = $ticket_category->category_name;
            $amount = number_format($ticket_actual_price, 2);
            $expires_at = date('h:ia', strtotime($ticket_category->expired_at));
            $owner_name = $requestData['owner_name'];
            $message = "Hello {$owner_name}, your {$ticket_category_name} ticket purchase for {$plate_number} (N{$amount}) was successful. Expires at {$expires_at}. Thank you.";
            // $message = "Hello, your ticket has been successfully purchased. Your ticket reference number is " . $ticket_vending->ticket_reference_number . ". Thank you for using AKSG-IRS.";
            //return $mobile_number;
            $this->send_sms_process_message("+234" . $mobile_number, $message);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket purchased successfully.',
            'data' => [
                'ticket' => new TicketVendingResource($ticket_vending),
            ]
        ], 200);
    }

    /**
     * Display specific resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br> And only ticket agent can vend ticket.
     */
    public function show(string $id)
    {
        $ticket_vending = TicketVending::find($id);
        if (!$ticket_vending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket vending not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket vending retrieved successfully.',
            'data' => new TicketVendingResource($ticket_vending),
        ], 200);
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
     * Ticket Vending Statistics for Agent.
     */
    public function ticket_statistics(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);

        $user = $request->user();
        $tickets_today = TicketVending::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
        $tickets_today_amount = TicketVending::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->sum('ticket_amount');

        //$tickets_this_week = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $now = Carbon::now();
        $tickets_this_week = TicketVending::where('user_id', $user->id)->whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->count();
        $tickets_this_week_amount = TicketVending::where('user_id', $user->id)->whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->sum('ticket_amount');
        //$tickets_this_week_amount = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('ticket_amount');
        $tickets_this_month = TicketVending::where('user_id', $user->id)->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        $tickets_this_month_amount = TicketVending::where('user_id', $user->id)->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('ticket_amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_today' => [
                    'total_tickets' => (int) $tickets_today,
                    'total_amount' => (float) $tickets_today_amount
                ],
                'tickets_this_week' => [
                    'total_tickets' => (int) $tickets_this_week,
                    'total_amount' => (float) $tickets_this_week_amount

                ],
                'tickets_this_month' => [
                    'total_tickets' => (int) $tickets_this_month,
                    'total_amount' => (float) $tickets_this_month_amount
                ]
            ]
        ], 200);
    }

    /**
     * Ticket Vending Statistics.
     */
    public function ticket_total_statistics(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);

        $tickets_today = TicketVending::whereDate('created_at', Carbon::today())->count();
        $tickets_today_amount = TicketVending::whereDate('created_at', Carbon::today())->sum('ticket_amount');

        //$tickets_this_week = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $now = Carbon::now();
        $tickets_this_week = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->count();
        $tickets_this_week_amount = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->sum('ticket_amount');
        //$tickets_this_week_amount = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('ticket_amount');
        $tickets_this_month = TicketVending::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        $tickets_this_month_amount = TicketVending::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('ticket_amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_today' => [
                    'total_tickets' => (int) $tickets_today,
                    'total_amount' => (float) $tickets_today_amount
                ],
                'tickets_this_week' => [
                    'total_tickets' => (int) $tickets_this_week,
                    'total_amount' => (float) $tickets_this_week_amount

                ],
                'tickets_this_month' => [
                    'total_tickets' => (int) $tickets_this_month,
                    'total_amount' => (float) $tickets_this_month_amount
                ]
            ]
        ], 200);
    }

    /**
     * Today's Ticket Collection Statistics.
     */
    public function today_collection()
    {
        //Carbon::setWeekStartsAt(Carbon::MONDAY);;
        $tickets_today_amount = TicketVending::whereDate('created_at', Carbon::today())->sum('ticket_amount');
        $ticket_categories = TicketCategory::where('category_status', 'active')->get();
        $today_collection = [];
        foreach ($ticket_categories as $key => $category) {
            $tickets_today_amount_in_category = TicketVending::whereDate('created_at', Carbon::today())->where('ticket_category_id', $category->id)->sum('ticket_amount');
            $today_collection[$key]['id'] = $key+1;
            $today_collection[$key]['category_name'] = $category->category_name;
            $today_collection[$key]['ticket_amount'] = $tickets_today_amount_in_category;
            $today_collection[$key]['percentage'] = number_format($tickets_today_amount_in_category/$tickets_today_amount*0.01, 2) . '%';
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved successfully.',
            'type' => 'today',
            'data' => $today_collection
        ]);
    }

    /**
     * Weekly's Ticket Collection Statistics.
     */
    public function weekly_collection()
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        $now = Carbon::now();
        $tickets_today_amount = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->sum('ticket_amount');
        $ticket_categories = TicketCategory::where('category_status', 'active')->get();
        $today_collection = [];
        foreach ($ticket_categories as $key => $category) {
            $tickets_today_amount_in_category = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->where('ticket_category_id', $category->id)->sum('ticket_amount');
            //return $tickets_today_amount_in_category;
            $today_collection[$key]['id'] = $key+1;
            $today_collection[$key]['category_name'] = $category->category_name;
            $today_collection[$key]['ticket_amount'] = $tickets_today_amount_in_category;
            $today_collection[$key]['percentage'] = $tickets_today_amount ? number_format($tickets_today_amount_in_category/$tickets_today_amount*0.01, 2) . '%' : 0 .'%';
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved successfully.',
            'type' => 'weekly',
            'data' => $today_collection
        ]);
    }

    /**
     * Monthly's Ticket Collection Statistics.
     */
    public function monthly_collection()
    {
        //Carbon::setWeekStartsAt(Carbon::MONDAY);
        $tickets_today_amount = TicketVending::whereBetween("created_at", [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('ticket_amount');
        $ticket_categories = TicketCategory::where('category_status', 'active')->get();
        $today_collection = [];
        foreach ($ticket_categories as $key => $category) {
            $tickets_today_amount_in_category = TicketVending::whereBetween("created_at", [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->where('ticket_category_id', $category->id)->sum('ticket_amount');
            //return $tickets_today_amount_in_category;
            $today_collection[$key]['id'] = $key+1;
            $today_collection[$key]['category_name'] = $category->category_name;
            $today_collection[$key]['ticket_amount'] = $tickets_today_amount_in_category;
            $today_collection[$key]['percentage'] = $tickets_today_amount ? number_format($tickets_today_amount_in_category/$tickets_today_amount*0.01, 2) . '%' : 0 .'%';
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved successfully.',
            'type' => 'monthly',
            'data' => $today_collection
        ]);
    }

    /**
     * Get Tickets by Agent ID resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return list of all ticket vending that been vended by the agent. <br>
     * Additional Query parameter are `limit` and `offset`. <br>
     * Additional Query parameter `?query=all`, return all resources
     * without pagination.
     */
    public function tickets_by_agent(Request $request, $ticket_agent_id)
    {
        $limit = 20;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }
        $ticket_vending = TicketVending::where('ticket_agent_id', $ticket_agent_id)->latest()->offset($offset)->limit($limit)->get();
        if ($request->has('query') && $request->get('query') == 'all') {
            $ticket_vending = TicketVending::where('ticket_agent_id', $ticket_agent_id)->latest()->get();
        }
        $total_number_of_records = TicketVending::where('ticket_agent_id', $ticket_agent_id)->count();
        if (!count($ticket_vending)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'ticket_vending_data' => [],
                    'total_number_of_records' => (int) $total_number_of_records
                ],
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_vending_data' => TicketVendingResource::collection($ticket_vending),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `plate_number` or `ticket_category_id`.<br>
     * Additonal Query paramters `phone_number`, `ticket_reference_number`, `ticket_agent_id`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $per_page = 20;

        if ($request->has('plate_number')) {
            $query_request = $request->get('plate_number');
            $ticket_response = TicketVending::where('plate_number', $query_request)->paginate($per_page);
        }
        if ($request->has('ticket_category_id')) {
            $query_request = $request->get('ticket_category_id');
            $ticket_response = TicketVending::where('ticket_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('phone_number')) {
            $query_request = $request->get('phone_number');
            $ticket_response = TicketVending::where('phone_number', $query_request)->paginate($per_page);
        }
        if ($request->has('ticket_reference_number')) {
            $query_request = $request->get('ticket_reference_number');
            $ticket_response = TicketVending::where('ticket_reference_number', $query_request)->paginate($per_page);
        }
        if ($request->has('ticket_agent_id')) {
            $query_request = $request->get('ticket_agent_id');
            $ticket_response = TicketVending::where('ticket_agent_id', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $ticket_response = TicketVending::whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($ticket_response)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return TicketVendingResource::collection($ticket_response);
    }
}
