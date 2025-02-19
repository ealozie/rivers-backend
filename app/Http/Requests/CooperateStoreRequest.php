<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CooperateStoreRequest extends FormRequest
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
            'rc_number' => 'required|unique:cooperates,rc_number',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'business_name' => 'required|string',
            'business_type_id' => 'required|exists:business_types,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'year_of_registration' => 'required|string',
            'date_of_business_commencement' => 'required|date',
            'website' => 'nullable|string',
            'tin_number' => 'nullable|string',
            'settlement_type_id' => 'required|exists:settlement_types,id',
            'business_category_id' => 'required|exists:business_categories,id',
            'business_sub_category_id' => 'required|exists:business_sub_categories,id',
            'business_level_id' => 'required|exists:business_levels,id',
            'demand_notice_category_id' => 'required|exists:demand_notice_categories,id',
            'number_of_staff' => 'required|integer',
            'monthly_turnover' => 'required|string',
            'picture_path' => 'nullable|string',
            'has_signage' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'city' => 'nullable',
            'street_name' => 'required|string',
            'street_number' => 'nullable',
            'landmark' => 'nullable',
            'state_id' => 'required|exists:states,id',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
            'property_id' => 'required|exists:properties,property_id',
            'street_id' => 'required|exists:streets,id',
        ];
    }
}
