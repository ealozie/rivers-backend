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
            'user_id' => 'nullable',
            'property_category_id' => 'required|exists:property_categories,id',
            'number_of_floors' => 'sometimes|integer',
            'property_type_id' => 'required|exists:property_types,id',
            'number_of_beds' => 'sometimes|integer',
            'number_of_rooms' => 'sometimes|integer',
            'plot_size' => 'nullable',
            'property_use_id' => 'required|exists:property_uses,id',
            'demand_notice_category_id' => 'required|exists:demand_notice_categories,id',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'has_borehole' => 'sometimes|boolean',
            'has_sewage' => 'sometimes|boolean',
            'is_connected_to_power' => 'sometimes|boolean',
            'has_fence' => 'sometimes|boolean',
            'notes' => 'nullable',
            'property_pictures' => 'nullable',
            'property_pictures.*' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'street_number' => 'sometimes|string',
            'street_name' => 'sometimes|string',
            'city' => 'nullable',
            'landmark' => 'nullable',
            'state_id' => 'required|exists:states,id',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
        ];
    }
}
