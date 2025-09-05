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
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'demand_notice_type' => 'required|in:blank,linked',
            'entity_id' => 'required_if:demand_notice_type,linked',
            'entity_type' => 'required_if:demand_notice_type,linked|in:individual,property,cooperate,signage,vehicle,shop',
        ];
    }
}
