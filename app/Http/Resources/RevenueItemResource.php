<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevenueItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
           // 'revenue_item_id' => $this->id,
            'revenue_name' => $this->revenue_name,
            'revenue_code' => $this->revenue_code,
            'fixed_fee' => $this->fixed_fee,
            'revenue_type' => $this->revenue_type->name,
            'agency' => new AgencyResource($this->agency),
        ];
    }
}
