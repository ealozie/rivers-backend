<?php

namespace App\Http\Resources;

use App\Http\Resources\PropertyPictureResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'owner' => $this->user->name,
            'property_category' => $this->propertyCategory->name ?? '',
            'number_of_floors' => $this->number_of_floors,
            'property_type' => $this->propertyType->name ?? '',
            'number_of_beds' => $this->number_of_beds,
            'number_of_rooms' => $this->number_of_rooms,
            'plot_size' => $this->plot_size,
            'property_use' => $this->propertyUse->name ?? '',
            'demand_notice_category' => $this->demandNoticeCategory->name ?? '',
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'has_borehole' => $this->has_borehole ? true : false,
            'has_sewage' => $this->has_sewage ? true : false,
            'is_connected_to_power' => $this->is_connected_to_power ? true : false,
            'has_fence' => $this->has_fence ? true : false,
            'notes' => $this->notes,
            'property_pictures' => PropertyPictureResource::collection($this->propertyPictures)

        ];
    }
}
