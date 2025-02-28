<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndividualUpdateRequest extends FormRequest
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
            "bvn" => "nullable|string",
            "nin" => "nullable|string",
            "tin" => "nullable|string",
            "nationality_id" => "nullable|exists:nationalities,id",
            "title_id" => "nullable|exists:titles,id",
            "surname" => "nullable|string",
            "first_name" => "nullable|string",
            "middle_name" => "nullable",
            "gender" => "nullable|string",
            "marital_status_id" => "nullable|exists:marital_statuses,id",
            "number_of_kids" => "nullable|integer",
            "date_of_birth" => "nullable|date",
            "approval_status" => "nullable|in:pending,approved,rejected",
            "blood_group_id" => "nullable|exists:blood_groups,id",
            "geno_type_id" => "nullable|exists:geno_types,id",
            "state_id" => "nullable|exists:states,id",
            "local_government_area_id" =>
                "nullable|exists:local_government_areas,id",
            "residence_local_government_area_id" =>
                "nullable|exists:local_government_areas,id",
            "residence_state_id" => "nullable|exists:states,id",
            "occupation_id" => "nullable|exists:occupations,id",
            "income_range" => "nullable",
            "street_number" => "nullable|string",
            "street_name" => "nullable|string",
            "city" => "nullable",
            "landmark" => "nullable",
            "street_id" => "nullable|exists:streets,id",
            "property_id" => "nullable|exists:properties,property_id",
        ];
    }
}
