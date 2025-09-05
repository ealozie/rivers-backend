<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandNoticeStoreRequest extends FormRequest
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
            'demand_notice_category_id' => 'required|integer',
            'entity_id' => 'nullable',
            'year_id' => 'required|integer',
            'entity_type' => 'nullable|in:individual,property,cooperate,signage,vehicle,shop',
            'quantity' => 'required|integer|min:1|max:200',
            'demand_notice_type' => 'required|in:blank,linked'
        ];
    }
}
