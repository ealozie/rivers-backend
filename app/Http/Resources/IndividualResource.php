<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndividualResource extends JsonResource
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
            "bvn" => $this->bvn,
            "nin" => $this->nin,
            "tin" => $this->tin,
            'street' => $this->street,
            'property' => $this->property,
            "nationality" => $this->nationality,
            "title" => $this->title,
            "surname" => $this->surname,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "gender" => $this->gender,
            "marital_status" => $this->marital_status,
            "number_of_kids" => $this->number_of_kids,
            "date_of_birth" => $this->date_of_birth,
            "blood_group" => $this->blood_group,
            "geno_type" => $this->geno_type,
            "state" => $this->state,
            "residence_state" => $this->residence_state ?? '',
            "landmark" => $this->landmark,
            "city" => $this->city,
            "street_name" => $this->street_name,
            "street_number" => $this->street_number,
            "facial_biometric_image_url" => $this->facial_biometric_image_url,
            "facial_biometric_status" => $this->facial_biometric_status,
            "registration_status" => $this->registration_status,
            "local_government_area" => $this->local_government_area,
            "residence_local_government_area" => $this->residence_local_government_area ?? '',
            "occupation" => $this->occupation,
            "income_range" => $this->income_range,
            "user" => $this->user,
            "demand_notice_category" => $this->demand_notice_category,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
