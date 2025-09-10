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
            'demand_notice_id' => $this->demand_notice_id,
            //'year_id' => $this->year_id,
            //'revenue_item_id' => $this->revenue_item_id,
            'revenue_item' => new RevenueItemResource($this->revenueItem),
            //'demand_notice' => new DemandNoticeResource($this->whenLoaded('demandNotice')),
            'year' => new AssessmentYearResource($this->year),
            'amount' => $this->amount,
            'payment_status' => $this->payment_status,
            'payment_receipt_number' => $this->payment_receipt_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
