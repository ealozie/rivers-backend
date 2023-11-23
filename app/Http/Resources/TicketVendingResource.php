<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TicketVendingResource extends JsonResource
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
            'plate_number' => $this->plate_number,
            'ticket_category' => $this->ticket_category->category_name ?? '',
            'amount' => number_format($this->amount, 2),
            'agent' => $this->user->name ?? '',
            'phone_number' => $this->phone_number ?? '',
            'ticket_reference_number' => $this->ticket_reference_number,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'ticket_status' => $this->ticket_status,
            'expire_at' => date('h:ia', strtotime($this->expired_at)),
            'expire_at_time' => Carbon::parse($this->expired_at)->diffForHumans(),
            'created_at' => (string) $this->created_at,
        ];
    }
}
