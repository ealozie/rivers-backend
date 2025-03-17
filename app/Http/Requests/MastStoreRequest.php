<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MastStoreRequest extends FormRequest
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
            'mast_location' => 'required|string|in:standalone,property',
            'property_id' => 'nullable|required_if:mast_location,property',
            'state_id' => 'nullable|integer|exists:states,id',
            'local_government_area_id' => 'nullable|integer|exists:local_government_areas,id',
            'street_name' => 'nullable|string',
            'street_number' => 'nullable|string',
            'city' => 'nullable|string',
            'mast_name' => 'required|string',
            'mast_use' => 'required|string',
            'owner_id' => 'nullable|exists:users,unique_id',
            'connected_to_power' => 'required|boolean',
            'connected_to_diesel_solar_power_generator' => 'required|boolean',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'note' => 'nullable|string',
            'pictures' => 'required|array|min:1|max:3',
            'pictures.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
}
