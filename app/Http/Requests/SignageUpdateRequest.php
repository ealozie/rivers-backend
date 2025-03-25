<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignageUpdateRequest extends FormRequest
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
            "height_in_meters" => "nullable|numeric",
            "width_in_meters" => "nullable|numeric",
            "longitude" => "nullable|numeric",
            "latitude" => "nullable|numeric",
            "street_name" => "nullable|string",
            "street_number" => "nullable|string",
            "city" => "nullable|string",
            "local_government_area_id" =>
                "nullable|exists:local_government_areas,id",
            "user_id" => "nullable|exists:users,id",
            "notes" => "nullable|string",
            "signage_location" => "nullable|string|in:property,standalone",
            "approval_status" => "nullable|in:pending,approved,rejected",
            "property_id" =>
                "nullable|required_if:signage_location,property|exists:properties,property_id",
            "street_id" => "nullable|exists:streets,id",
            'signage_pictures.*' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
