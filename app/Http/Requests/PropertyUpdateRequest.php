<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyUpdateRequest extends FormRequest
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
            "user_id" => "nullable",
            "property_category_id" => "nullable|exists:property_categories,id",
            "number_of_floors" => "nullable|integer",
            "property_type_id" => "nullable|exists:property_types,id",
            "number_of_beds" => "nullable|integer",
            "number_of_rooms" => "nullable|integer",
            "plot_size" => "nullable",
            "property_use_id" => "nullable|exists:property_uses,id",
            "demand_notice_category_id" =>
                "nullable|exists:demand_notice_categories,id",
            "longitude" => "nullable",
            "latitude" => "nullable",
            "has_borehole" => "nullable|boolean",
            "has_sewage" => "nullable|boolean",
            "is_connected_to_power" => "nullable|boolean",
            "approval_status" => "nullable|in:pending,approved,rejected",
            "has_fence" => "nullable|boolean",
            "notes" => "nullable",
            "property_pictures" => "nullable",
            "property_pictures.*" => "nullable|mimes:jpeg,png,jpg|max:2048",
            "street_number" => "nullable|string",
            "street_name" => "nullable|string",
            "city" => "nullable",
            "landmark" => "nullable",
            "state_id" => "nullable|exists:states,id",
            "local_government_area_id" =>
                "nullable|exists:local_government_areas,id",
            "street_id" => "nullable|exists:streets,id",
        ];
    }
}
