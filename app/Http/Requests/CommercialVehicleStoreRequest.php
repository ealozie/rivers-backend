<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommercialVehicleStoreRequest extends FormRequest
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
        //create validation rules for the request
        return [
            "user_id" => "required|exists:users,unique_id",
            "plate_number" => "required|string",
            "vehicle_category_id" => "required|exists:vehicle_categories,id",
            "vehicle_manufacturer_id" =>
                "required|exists:vehicle_manufacturers,id",
            "vehicle_model_id" => "required|exists:vehicle_models,id",
            "chassis_number" => "required",
            "engine_number" => "required",
            "ticket_category_id" => "required|exists:ticket_categories,id",
            "capacity" => "nullable",
            "routes" => "nullable",
            "driver_id" => "required|exists:users,unique_id",
            "driver_license_number" => "nullable",
            "driver_license_expiry_date" => "nullable",
            "driver_license_image" => "sometimes|mimes:jpeg,jpg,png|max:2048",
            "permit_renewal_count" => "sometimes|integer",
            "permit_number" => "sometimes|string",
            "permit_expiry_date" => "sometimes|string",
            "permit_image" => "sometimes|mimes:jpeg,jpg,png|max:2048",
            "note" => "nullable",
            "street_id" => "required|exists:streets,id",
        ];
    }
}
