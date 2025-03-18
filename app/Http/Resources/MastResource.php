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
            'mast_id' => $this->mast_id,
            'mast_location' => $this->mast_location,
            'property' => $this->property_id && $this->property ? new PropertyResource($this->property) : null,
            'state' => $this->state,
            'local_government_area' => $this->local_government_area,
            'street_name' => $this->street_name,
            'street_number' => $this->street_number,
            'city' => $this->city,
            'approval_status' => $this->approval_status,
            'mast_name' => $this->mast_name,
            'mast_use' => $this->mast_use,
            'owner' => $this->owner,
            'connected_to_power' => $this->connected_to_power ? true : false,
            'connected_to_diesel_solar_power_generator' => $this->connected_to_diesel_solar_power_generator ? true : false,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'note' => $this->note,
            'created_by' => $this->created_by ? $this->created_user : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pictures' => $this->pictures->pluck('image_path')
        ];
    }
}
