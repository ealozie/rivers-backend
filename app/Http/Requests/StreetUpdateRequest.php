<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StreetUpdateRequest extends FormRequest
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
            'local_government_area_id' => 'nullable|exists:local_government_areas,id',
            'name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
