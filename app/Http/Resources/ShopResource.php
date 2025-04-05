<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'street' => $this->street,
            "shop_id" => $this->shop_id,
            "name" => $this->name,
            "number" => $this->number,
            'property' => $this->property,
            "zone" => $this->zone,
            "location" => $this->location,
            "location_type" => $this->location_type,
            "market_name" => $this->market_name,
            "street_name" => $this->street_name,
            "street_number" => $this->street_number,
            "city" => $this->city,
            "local_government_area" => $this->local_government_area,
            "business_category" => $this->business_category,
            "business_sub_category" => $this->business_sub_category,
            "classification" => $this->classification,
            "user" => $this->user,
            "status" => $this->status,
            "notes" => $this->notes,
            "approval_status" => $this->approval_status,
            "shop_occupants" => ShopOccupantListResource::collection($this->shop_occupants),
            //"added_by" => $this->added_by,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,

        ];
    }
}
