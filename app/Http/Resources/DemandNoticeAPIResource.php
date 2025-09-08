<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandNoticeAPIResource extends JsonResource
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
            'demand_notice_category' => $this->demand_notice_category->name ?? '',
            'year' => $this->year->year,
            //'demand_notice_status' => $this->status,
            'demand_notice_items' => DemandNoticeItemResource::collection($this->demand_notice_items),
            'created_at' => $this->created_at,
        ];
    }
}
