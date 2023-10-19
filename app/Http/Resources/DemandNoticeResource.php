<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandNoticeResource extends JsonResource
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
            'demand_notice_number' => $this->demand_notice_number,
            'demand_notice_category' => $this->demand_notice_category,
            'user' => $this->user,
            'year' => $this->year,
            'generated_by' => $this->user_generated,
            'served_by' => $this->user_served,
            'date_served' => $this->date_served,
            'enforcement_begins_at' => $this->enforcement_begins_at,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'demand_notice_status' => $this->status,
            'has_been_served' => $this->has_been_served,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'demand_notice_items' => $this->demand_notice_items,
        ];
    }
}
