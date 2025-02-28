<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommercialVehicleResource extends JsonResource
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
            "vehicle_id" => $this->vehicle_id,
            'street' => $this->street,
            'property' => $this->property,
            "user" => new IndividualUserResource($this->user),
            "plate_number" => $this->plate_number,
            "vehicle_category" => new VehicleCategoryResource($this->vehicle_category),
            "vehicle_manufacturer" => new VehicleManufacturerResource($this->vehicle_manufacturer),
            "vehicle_model" => new VehicleModelResource($this->vehicle_model),
            "chassis_number" => $this->chassis_number,
            "engine_number" => $this->engine_number,
            "ticket_category" => new TicketCategoryResource($this->ticket_category),
            "capacity" => $this->capacity,
            "routes" => $this->routes,
            "driver" => new IndividualUserResource($this->driver),
            "driver_license_number" => $this->driver_license_number,
            "driver_license_expiry_date" => $this->driver_license_expiry_date,
            "driver_license_image" => $this->driver_license_image,
            "permit_renewal_count" => $this->permit_renewal_count,
            "permit_number" => $this->permit_number,
            "permit_expiry_date" => $this->permit_expiry_date,
            "permit_image" => $this->permit_image,
            "note" => $this->note,
            "status" => $this->status,
            //"added_by" => $this->added_by,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
