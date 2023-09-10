<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopStoreRequest extends FormRequest
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
            'name' => 'required',
            'zone' => 'sometimes|string',
            'shop_number' => 'sometimes|string',
            'location' => 'sometimes|string',
            'market_name_id' => 'required|exists:market_names,id',
            'street_name' => 'sometimes|string',
            'street_number' => 'sometimes|string',
            'city' => 'sometimes|string',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
            'business_category_id' => 'required|exists:business_categories,id',
            'business_sub_category_id' => 'required|exists:business_sub_categories,id',
            'classification_id' => 'required|exists:classifications,id',
            'user_id' => 'required|exists:users,id',
            'notes' => 'sometimes|string',
        ];
    }
}
