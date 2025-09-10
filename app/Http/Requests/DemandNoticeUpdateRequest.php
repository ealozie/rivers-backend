<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandNoticeUpdateRequest extends FormRequest
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
            'served_by' => 'required|exists:users,id',
            'date_served' => 'required|date',
            'comments' => 'nullable|string|max:1000',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
            'demand_notice_type' => 'required|in:blank,linked',
            'entity_id' => 'required_if:demand_notice_type,linked',
            //'entity_type' => 'required_if:demand_notice_type,linked|in:individual,property,cooperate,signage,vehicle,shop',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'served_by.required' => 'The served by field is required.',
            'served_by.exists' => 'The selected served by user does not exist.',
            'date_served.required' => 'The date served field is required.',
            'date_served.date' => 'The date served must be a valid date.',
            'comments.required' => 'The comments field is required.',
            'comments.string' => 'The comments must be a string.',
            'comments.max' => 'The comments may not be greater than 1000 characters.',
            'longitude.required' => 'The longitude field is required.',
            'longitude.numeric' => 'The longitude must be a number.',
            'longitude.between' => 'The longitude must be between -180 and 180.',
            'latitude.required' => 'The latitude field is required.',
            'latitude.numeric' => 'The latitude must be a number.',
            'latitude.between' => 'The latitude must be between -90 and 90.',
            'demand_notice_type.required' => 'The demand notice type field is required.',
            'demand_notice_type.in' => 'The demand notice type must be either blank or linked.',
            'entity_id.required_if' => 'The entity ID field is required when demand notice type is linked.',
        ];
    }
}
