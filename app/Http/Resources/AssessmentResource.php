<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
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
            'user' => $this->user_id ? new UserResource($this->user) : '',
            'revenue_item' => new RevenueItemResource($this->revenue_item),
            'agency' => new AgencyResource($this->agency),
            'full_name' => $this->full_name,
            "email" => $this->email,
            "phone_number" => $this->phone_number,
            "contact_address" => $this->contact_address,
            "amount" => $this->amount,
            "assessment_reference" => $this->assessment_reference,
            "receipt_number" => $this->receipt_number,
            "assessment_year_id" => new AssessmentYearResource($this->assessment_year),
            "status" => $this->status,
            "payment_status" => $this->payment_status,
            "added_by" => $this->added_by ? new UserResource($this->added_by_user) : '',
        ];
    }
}
