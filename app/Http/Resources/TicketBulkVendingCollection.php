<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TicketBulkVendingCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plate_number' => $this->plate_number,
            'ticket_category' => $this->ticket_category->category_name ?? '',
            'amount' => number_format($this->amount, 2),
            'agent_discount' => $this->agent_discount,
            'agent' => $this->user->name ?? '',
            'status' => $this->status,
            'total_tickets' => $this->total_tickets,
            'remaining_tickets' => $this->remaining_tickets,
            'ticket_status' => $this->ticket_status,
            'expire_at' => date('h:ia', strtotime($this->expired_at)),
            'expire_at_time' => Carbon::parse($this->expired_at)->diffForHumans(),
            'created_at' => (string) $this->created_at,
        ];
    }
}
