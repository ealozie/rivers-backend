<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CooperateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //'rc_number' => 'required|unique:cooperates,rc_number',
            "business_name" => "nullable|string",
            "business_type_id" => "nullable|exists:business_types,id",
            "longitude" => "nullable|numeric",
            "latitude" => "nullable|numeric",
            "year_of_registration" => "nullable|string",
            "date_of_business_commencement" => "nullable|date",
            "website" => "nullable|string",
            "tin_number" => "nullable|string",
            "settlement_type_id" => "nullable|exists:settlement_types,id",
            "business_category_id" => "nullable|exists:business_categories,id",
            "business_sub_category_id" =>
                "nullable|exists:business_sub_categories,id",
            "business_level_id" => "nullable|exists:business_levels,id",
            "demand_notice_category_id" =>
                "nullable|exists:demand_notice_categories,id",
            "number_of_staff" => "nullable|integer",
            "monthly_turnover" => "nullable|string",
            "picture_path" => "nullable|string",
            "has_signage" => "nullable|boolean",
            "notes" => "nullable|string",
            "city" => "nullable",
            "street_name" => "nullable|string",
            "street_number" => "nullable",
            "landmark" => "nullable",
            "approval_status" => "nullable|in:pending,approved,rejected",
            "state_id" => "nullable|exists:states,id",
            "local_government_area_id" =>
                "nullable|exists:local_government_areas,id",
            "property_id" => "nullable|exists:properties,property_id",
            "street_id" => "nullable|exists:streets,id",
        ];
    }
}
