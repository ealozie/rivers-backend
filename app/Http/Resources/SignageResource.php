<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignageResource extends JsonResource
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
            'property' => $this->property,
            "signage_id" => $this->signage_id,
            "height_in_meters" => $this->height_in_meters,
            "width_in_meters" => $this->width_in_meters,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "street_name" => $this->street_name,
            "street_number" => $this->street_number,
            "city" => $this->city,
            "local_government_area" => $this->local_government_area,
            "user" => $this->user,
            //"added_by" => $this->added_by_user,
            "notes" => $this->notes,
            "created_at" => $this->created_at,
            //"updated_at" => $this->updated_at,
        ];
    }
}
