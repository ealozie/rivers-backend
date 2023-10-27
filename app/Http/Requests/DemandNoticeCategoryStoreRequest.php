<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandNoticeCategoryStoreRequest extends FormRequest
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
            'name' => 'required|string|unique:demand_notice_categories',
            'enforcement_duration' => 'required|numeric',
            'status' => 'required|string',
        ];
    }
}
