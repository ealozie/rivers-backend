<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketVendingStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_amount' => $this->ticket_amount,
            'ticket_discounted_amount' => $this->ticket_discounted_amount,
            'total_tickets' => $this->total_tickets,
            'ticket_date' => $this->ticket_date,
            'created_at' => $this->created_at,
        ];
    }
}
