<?php

namespace App\Http\Controllers;

use App\Models\TicketBulkVending;
use App\Models\TicketCategory;
use App\Models\TicketVending;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

class TicketBulkVendingProcess extends Controller
{
    /**
     * Handle the incoming request.
     */
    use SendSMS;
    public function __invoke(Request $request)
    {
        $ticket_bulk_vending = TicketBulkVending::where('status', 'active')->get();
        foreach ($ticket_bulk_vending as $vending) {
            $ticket_vending = new TicketVending();
            $ticket_vending->plate_number = $vending->plate_number;
            $ticket_vending->ticket_category_id = $vending->ticket_category_id;
            $ticket_vending->amount = $vending->amount;
            $ticket_vending->discounted_price = $vending->discounted_price;
            $ticket_vending->owner_name = $vending->owner_name;
            $ticket_vending->ticket_amount = $vending->ticket_amount;
            $ticket_vending->agent_discount = $vending->agent_discount;
            $ticket_vending->ticket_agent_id = $vending->ticket_agent_id;
            $ticket_vending->user_id = $vending->user_id;
            $ticket_vending->phone_number = $vending->phone_number;
            $ticket_vending->latitude = $vending->latitude;
            $ticket_vending->longitude = $vending->longitude;
            $ticket_vending->expired_at = $vending->expired_at;
            $ticket_vending->ticket_status = 'active';
            $ticket_vending->vending_source = 'bulk_vending';
            $ticket_vending->ticket_reference_number = date('isYd');
            $ticket_vending->save();
            $vending->decrement('remaining_tickets');
            if ($vending->remaining_tickets == 0) {
                $vending->status = 'inactive';
                $vending->save();
            }

            if ($vending->phone_number) {
                $mobile_number = ltrim($vending->phone_number, "0");
                $ticket_category = TicketCategory::find($vending->ticket_category_id);
                $plate_number = $vending->plate_number;
                $ticket_category_name = $ticket_category->category_name;
                $amount = number_format($vending->ticket_amount, 2);
                $expires_at = date('h:ia', strtotime($ticket_category->expired_at));
                $message = "Hello, your {$ticket_category_name} ticket purchase for {$plate_number} (N{$amount}) was successful. Expires at {$expires_at}. Thank you.";
                // $message = "Hello, your ticket has been successfully purchased. Your ticket reference number is " . $ticket_vending->ticket_reference_number . ". Thank you for using AKSG-IRS.";
                //return $mobile_number;
                $this->send_sms_process_message("+234" . $mobile_number, $message);
            }
        }
    }
}
