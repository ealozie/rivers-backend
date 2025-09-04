<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandNoticeItemResource extends JsonResource
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
            'year_id' => $this->year_id,
            'demand_notice_id' => $this->demand_notice_id,
            //'agency' => new AgencyResource($this->agency),
            'revenue' => new RevenueItemResource($this->revenueItem),
            'amount' => $this->amount,
            'payment_status' => $this->payment_status,
        ];
    }
}
