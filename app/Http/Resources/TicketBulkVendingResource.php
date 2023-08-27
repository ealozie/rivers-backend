<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TicketBulkVendingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'plate_number' => $this->plate_number,
            'ticket_category' => $this->ticket_category->category_name,
            'ticket_amount' => number_format($this->ticket_amount, 2),
            'discount' => $this->agent_discount . '%',
            'amount' => number_format($this->amount, 2),
            'number_of_tickets' => $this->total_tickets,
            'ticket_status' => $this->status,
            'expire_at' => date('h:ia', strtotime($this->expired_at)),
            'expire_at_time' => Carbon::parse($this->expired_at)->diffForHumans(),
            'created_at' => $this->created_at,
            'time_created' => $this->created_at->diffForHumans(),
        ];
    }
}
