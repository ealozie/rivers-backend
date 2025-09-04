<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandNoticeCategoryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "demand_notice_category_id" => $this->demand_notice_category,
            "revenue_item" => new RevenueItemResource($this->revenue_item),
            "amount" => $this->amount,
            "added_by" => $this->added_by_user,
            "status" => $this->status,
            "created_at" => $this->created_at,
        ];
    }
}
