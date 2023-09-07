<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketEnforcementResource extends JsonResource
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
            'ticket_category' => $this->ticket_category->category_name ?? '', 
            'response' => json_decode($this->response),
            'created_at' => $this->created_at,
            'status' => $this->status
        ];
    }
}
