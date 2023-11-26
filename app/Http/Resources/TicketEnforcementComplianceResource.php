<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketEnforcementComplianceResource extends JsonResource
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
            'phone_number' => $this->phone_number,
            'enforcement_source' => $this->enforcement_source,
            //'ticket_category' => $this->ticket_category->category_name ?? '',
            //'agent' => $this->ticket_agent->user->name ?? '',
            //'longitude' => $this->longitude,
            //'latitude' => $this->latitude,
            //'response' => json_decode($this->response),
            //'created_at' => (string) $this->created_at,
            //'status' => $this->status,
            'vending_status' => $this->vending_status,
            'ticket_vendings' => $this->ticket_vendings
        ];
    }
}
