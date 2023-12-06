<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use App\Models\TicketVending;
use App\Traits\SendSMS;

class CheckForExpiredTickets extends Controller
{
    /**
     * Handle the incoming request.
     */
    use SendSMS;
    public function __invoke()
    {
        //Check if there are any tickets that has expired
        //If there are, set their status to expired
        //Send email to the user that their ticket has expired
        $ticket_vendings = TicketVending::where('ticket_status', 'active')->get();
        foreach ($ticket_vendings as $ticket_vending) {
            $current_time = strtotime(date('H:i:s'));
            $ticket_expired_time = strtotime($ticket_vending->expired_at);
            if ($current_time > $ticket_expired_time) {
                $ticket_vending->ticket_status = 'expired';
                $ticket_vending->save();

                //Send SMS to user
                $mobile_number = ltrim($ticket_vending->phone_number, "0");
                $ticket_category = TicketCategory::find($ticket_vending->ticket_category_id);
                $category_name = $ticket_category->category_name;
                $amount = number_format($ticket_category->amount, 2);
                $plate_number = $ticket_vending->plate_number;
                $expires_at = date('h:ia', strtotime($ticket_category->expired_at));
                $owner_name = $ticket_vending->owner_name;
                $message = "Hello {$owner_name}, your {$category_name} ticket purchase for {$plate_number} (N{$amount}) expired, {$expires_at}.";
                $this->send_sms_process_message("+234" . $mobile_number, $message);
            }
        }
    }
}
