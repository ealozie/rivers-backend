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
            'id' => $this->ticket_catogory_id,
            'ticket_category' => $this->ticket_category->category_name,
            'ticket_amount' => $this->ticket_category->amount,
            'discount' => (int) $this->discount,
            'status' => $this->status,
            'created_at' => (string) $this->created_at,
        ];
    }
}
