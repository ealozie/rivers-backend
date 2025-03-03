<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignageStoreRequest extends FormRequest
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
            'height_in_meters' => 'required|numeric',
            'width_in_meters' => 'required|numeric',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'street_name' => 'nullable|string',
            'street_number' => 'required|string',
            'city' => 'required|string',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
            'user_id' => 'required|exists:users,unique_id',
            'notes' => 'nullable|string',
            'signage_location' => 'required|string|in:property,standalone',
            'property_id' => 'nullable|required_if:signage_location,property|exists:properties,property_id',
            'street_id' => 'nullable|exists:streets,id',
        ];
    }
}
