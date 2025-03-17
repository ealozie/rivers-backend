<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentStoreRequest extends FormRequest
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
            'full_name' => 'required|string',
            'phone_number' => 'required|string',
            'status' => 'nullable',
            'email' => 'required|email',
            'agency_id' => 'required|exists:agencies,id',
            'revenue_item_id' => 'required|exists:revenue_items,id',
            'contact_address' => 'required|string',
            'amount' => 'required|numeric',
            'assessment_year_id' => 'required|exists:assessment_years,id',
            'assessment_entity_id' => 'required',
            'entity_type' => 'required|in:vehicle,shop,individual,cooperate,signage,property,mast',
            'due_date' => 'required|date',
        ];
    }
}
