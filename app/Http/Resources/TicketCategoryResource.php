<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketCategoryResource extends JsonResource
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
            'name' => $this->category_name,
            'amount' => $this->amount,
            'status' => $this->category_status,
            'allow_multiple_ticket_purchase' => $this->allow_multiple_ticket_purchase ? true : false,
            'duration_in_days' => $this->duration,
            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,
        ];
    }
}
