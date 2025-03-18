<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MastUpdateRequest extends FormRequest
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
            "mast_location" => "nullable|string|in:standalone,property",
            "property_id" => "nullable|exists:properties,property_id",
            "state_id" => "nullable|integer|exists:states,id",
            "local_government_area_id" =>
                "nullable|exists:local_government_areas,id",
            "street_name" => "nullable|string",
            "street_number" => "nullable|string",
            "city" => "nullable|string",
            "mast_name" => "nullable|string",
            "approval_status" => "nullable",
            "mast_use" => "nullable|string",
            "owner_id" => "nullable|exists:users,unique_id",
            "connected_to_power" => "nullable|boolean",
            "connected_to_diesel_solar_power_generator" => "nullable|boolean",
            "longitude" => "nullable|numeric",
            "latitude" => "nullable|numeric",
            "note" => "nullable|string",
            "pictures" => "nullable|array|min:1|max:3",
            "pictures.*" =>
                "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ];
    }
}
