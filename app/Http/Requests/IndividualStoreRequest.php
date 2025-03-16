<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndividualStoreRequest extends FormRequest
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
            'bvn' => 'sometime|string',
            'nin' => 'sometime|string',
            'tin' => 'sometime|string',
            'registration_option' => 'required|in:nin,tin,bvn,no_id',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:individuals,phone_number',
            'nationality_id' => 'required|exists:nationalities,id',
            'title_id' => 'required|exists:titles,id',
            'surname' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable',
            'gender' => 'required|string',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'number_of_kids' => 'required|integer',
            'date_of_birth' => 'required|date',
            'blood_group_id' => 'nullable|exists:blood_groups,id',
            'geno_type_id' => 'nullable|exists:geno_types,id',
            'state_id' => 'nullable|exists:states,id',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
            'residence_local_government_area_id' => 'required|exists:local_government_areas,id',
            'residence_state_id' => 'required|exists:states,id',
            'occupation_id' => 'required|exists:occupations,id',
            'income_range' => 'required',
            'street_number' => 'nullable|string',
            'street_name' => 'nullable|string',
            'city' => 'nullable',
            'landmark' => 'nullable',
            'street_id' => 'nullable|exists:streets,id',
            'property_id' => 'nullable|exists:properties,property_id',
        ];
    }
}
