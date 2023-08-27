<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TicketVendingResource;
use App\Models\CommercialVehicle;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use App\Models\TicketAgentWallet;
use App\Models\TicketCategory;
use App\Models\TicketVending;
use App\Traits\SendSMS;

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
     * Return list of all ticket categories that been vended by current authenticated agent.
     */
    public function index(Request $request)
    {
        $ticket_vending = TicketVending::where('user_id', $request->user()->id)->latest()->get();
        if (!count($ticket_vending)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No ticket vending found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket vending list retrieved successfully.',
            'data' => TicketVendingResource::collection($ticket_vending),
        ], 200);
    }

    /**
     * Store a resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br> And only ticket agent can vend ticket.
     */
    public function store(Request $request)
    {

        $requestData = $request->validate([
            'plate_number' => 'required|string',
            'phone_number' => 'required|string',
            'ticket_category_id' => 'sometimes|required|integer'
        ]);
        $plate_number = strtoupper($requestData['plate_number']);
        $phone_number = $requestData['phone_number'];

        $commercial_vehicle = CommercialVehicle::where('plate_number', $plate_number)->first();
        if (!$commercial_vehicle && !$request->has('ticket_category_id')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Plate number not found. Select a ticket category to continue.',
            ], 404);
        }
        $response_data = [];
        $ticket_category_id = $requestData['ticket_category_id'];
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
                'message' => 'You are not allowed to vend this ticket  category.',
            ], 403);
        }

        //Check if category accept multiple and if agent has purchased for that day.
        $ticket_category = TicketCategory::find($response_data['category_id']);

        if (!$ticket_category->allow_multiple_ticket_purchase) {
            $ticket_vending = TicketVending::where('plate_number', $plate_number)->where('ticket_category_id', $response_data['category_id'])->whereDate('created_at', date('Y-m-d'))->first();
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
        $ticket_price = $ticket_category->amount;
        $ticket_actual_price = $ticket_category->amount;
        if ($ticket_agent->discount) {
            $price = $ticket_price - ($ticket_price * $ticket_agent->discount / 100);
            $ticket_price = round($price);
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
            $ticket_vending->ticket_category_id = $response_data['category_id'];
            $ticket_vending->amount = $ticket_price;
            $ticket_vending->ticket_amount = $ticket_actual_price;
            $ticket_vending->agent_discount = $ticket_agent->discount;
            $ticket_vending->ticket_agent_id = $ticket_agent->id;
            $ticket_vending->user_id = $user->id;
            $ticket_vending->expired_at = $ticket_category->expired_at;
            $ticket_vending->ticket_status = 'active';
            $ticket_vending->ticket_reference_number = strtoupper(uniqid('AKSIGR')) . date('YmdHis');
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
            $message = "Hello, your ticket has been successfully purchased. Your ticket reference number is " . $ticket_vending->ticket_reference_number . ". Thank you for using AKSG-IRS.";
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
                'is_enumerated' => $is_enumerated,
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
}
