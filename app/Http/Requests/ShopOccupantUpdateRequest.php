<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopOccupantUpdateRequest extends FormRequest
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
            'shop_id' => 'nullable|exists:shops',
            'occupant_id' => 'nullable|exists:individuals,individual_id'
        ];
    }
}
