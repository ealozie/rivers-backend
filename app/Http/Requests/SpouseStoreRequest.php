<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpouseStoreRequest extends FormRequest
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
            'individual_id' => 'required|exists:individuals,id',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'surname' => 'required|string',
            'occupation_id' => 'required|exists:occupations,id',
            'phone_number' => 'required|string|max:11, min:11|unique:spouses,phone_number',
        ];
    }
}
