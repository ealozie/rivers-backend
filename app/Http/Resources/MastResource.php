<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MastResource extends JsonResource
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
            'mast_location' => $this->mast_location,
            'property_id' => $this->property_id,
            'state_id' => $this->state,
            'local_government_area_id' => $this->local_government_area,
            'street_name' => $this->street_name,
            'street_number' => $this->street_number,
            'city' => $this->city,
            'mast_name' => $this->mast_name,
            'mast_use' => $this->mast_use,
            'owner_id' => $this->owner_id,
            'connected_to_power' => $this->connected_to_power,
            'connected_to_diesel_solar_power_generator' => $this->connected_to_diesel_solar_power_generator,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'note' => $this->note,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pictures' => $this->pictures->pluck('image_path')
        ];
    }
}
