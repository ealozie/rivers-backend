<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CooperateResource extends JsonResource
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
            "cooperate_id" => $this->cooperate_id,
            "rc_number" => $this->rc_number,
            "user" => new UserResource($this->user),
            "business_name" => $this->business_name,
            "business_type" => $this->business_type,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "year_of_registration" => $this->year_of_registration,
            "date_of_business_commencement" => $this->date_of_business_commencement,
            "website" => $this->website,
            "tin_number" => $this->tin_number,
            "settlement_type" => $this->settlement_type,
            "business_category" => $this->business_category,
            "business_sub_category" => $this->business_sub_category,
            "business_level" => $this->business_level,
            "demand_notice_category" => $this->demand_notice_category,
            "number_of_staff" => $this->number_of_staff,
            "monthly_turnover" => $this->monthly_turnover,
            "picture_path" => $this->picture_path,
            "has_signage" => $this->has_signage,
            "notes" => $this->notes,
            "city" => $this->city,
            "street_name" => $this->street_name,
            "street_number" => $this->street_number,
            "landmark" => $this->landmark,
            "local_government_area" => $this->local_government_area,
            'state' => $this->state,
            "user_id" => $this->user_id,
            "added_by" => $this->added_by,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
