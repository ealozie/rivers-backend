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
            'id' => $this->id,
            'ticket_category' => $this->ticket_category->category_name,
            'discount' => $this->discount,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
