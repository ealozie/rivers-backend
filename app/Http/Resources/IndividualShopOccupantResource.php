<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndividualShopOccupantResource extends JsonResource
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
            "individual_id" => $this->individual_id,
            "email_address" => $this->email_address,
            "phone_number" => $this->phone_number,
            "surname" => $this->surname,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "gender" => $this->gender,
        ];
    }
}
