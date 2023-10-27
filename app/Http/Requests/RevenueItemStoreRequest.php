<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevenueItemStoreRequest extends FormRequest
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
            'agency_id' => 'required|numeric',
            'revenue_name' => 'required|string|unique:revenue_items',
            'revenue_code' => 'required|string|unique:revenue_items',
            'fixed_fee' => 'required|numeric',
            'revenue_type_id' => 'required|numeric',
            'notes' => 'sometimes|string',
        ];
    }
}
