<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketAgentCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->ticket_category_id,
            'ticket_category' => $this->ticket_category->category_name,
            'ticket_amount' => (int) $this->ticket_category->amount,
            'discount' => (int) $this->discount,
            'allow_multiple_ticket_purchase' => $this->check_status($this->ticket_category->allow_multiple_ticket_purchase),
            'allow_multiple_quantity' => $this->check_status($this->ticket_category->allow_multiple_quantity),
            'status' => $this->status,
            'created_at' => (string) $this->created_at,
        ];
    }

    public function check_status($value)
    {
        if ($value == 1) {
            return true;
        } else {
            return false;
        }
        
    }
}
