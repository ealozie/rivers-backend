<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopUpdateRequest extends FormRequest
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
            "name" => "nullable",
            "zone" => "nullable|string",
            "number" => "nullable",
            "location_type" => "nullable|in:market,street",
            "location" => "nullable|string",
            "market_name_id" => "nullable|exists:market_names,id",
            "street_name" => "nullable",
            "street_number" => "nullable",
            "city" => "nullable",
            "approval_status" => "nullable|in:pending,approved,rejected",
            "local_government_area_id" =>
                "nullable|exists:local_government_areas,id",
            "business_category_id" => "nullable|exists:business_categories,id",
            "business_sub_category_id" =>
                "nullable|exists:business_sub_categories,id",
            "classification_id" => "nullable|exists:classifications,id",
            "user_id" => "nullable|exists:users,unique_id",
            "notes" => "nullable",
            "street_id" => "nullable|exists:streets,id",
            "property_id" => "nullable|exists:properties,property_id",
        ];
    }
}
