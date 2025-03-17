<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopStoreRequest extends FormRequest
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
            'name' => 'required',
            'zone' => 'sometimes|string',
            'number' => 'nullable',
            'location_type' => 'required|in:market,street',
            'location' => 'sometimes|string',
            'market_name_id' => 'nullable|exists:market_names,id',
            'street_name' => 'nullable',
            'street_number' => 'nullable',
            'city' => 'nullable',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
            'business_category_id' => 'required|exists:business_categories,id',
            'business_sub_category_id' => 'required|exists:business_sub_categories,id',
            'classification_id' => 'required|exists:classifications,id',
            'user_id' => 'nullable',
            'notes' => 'nullable',
            'street_id' => 'required|exists:streets,id',
            'property_id' => 'nullable|exists:properties,property_id',
        ];
    }
}
