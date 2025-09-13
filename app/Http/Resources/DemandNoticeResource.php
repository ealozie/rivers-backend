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
            'entity_type' => $this->demand_noticeable_type,
            'entity' => $this->demand_noticeable,
            'user' => $this->user,
            'year' => $this->year,
            'demand_notice_type' => $this->demand_notice_type,
            'generated_by' => $this->user_generated,
            'served_by' => $this->user_served,
            'date_served' => $this->date_served,
            'enforcement_begins_at' => $this->enforcement_begins_at,
            'comments' => $this->comments,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'demand_notice_status' => $this->status,
            'has_been_served' => $this->has_been_served,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'demand_notice_items' => DemandNoticeItemResource::collection($this->demand_notice_items),
        ];
    }
}
