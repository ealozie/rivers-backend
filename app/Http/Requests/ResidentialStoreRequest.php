<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResidentialStoreRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'street_name' => 'required|string',
            'street_number' => 'required|string',
            'landmark' => 'required|string',
            'city' => 'required|string',
            'state_id' => 'required|exists:states,id',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
        ];
    }
}
