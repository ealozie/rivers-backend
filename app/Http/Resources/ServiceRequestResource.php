<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
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
            'service_sub_category' => $this->service_sub_category,
            'user' => $this->user,
            'request_id' => $this->request_id,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at
        ];
    }
}
