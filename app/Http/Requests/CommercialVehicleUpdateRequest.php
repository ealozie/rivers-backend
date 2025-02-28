<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommercialVehicleUpdateRequest extends FormRequest
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
            "user_id" => "nullable|exists:users,id",
            "plate_number" => "nullable|string",
            "vehicle_category_id" => "nullable|exists:vehicle_categories,id",
            "vehicle_manufacturer_id" =>
                "nullable|exists:vehicle_manufacturers,id",
            "vehicle_model_id" => "nullable|exists:vehicle_models,id",
            "chassis_number" => "nullable",
            "engine_number" => "nullable",
            "ticket_category_id" => "nullable|exists:ticket_categories,id",
            "capacity" => "nullable|string",
            "routes" => "nullable|string",
            "driver_id" => "nullable|exists:users,id",
            "driver_license_number" => "nullable|string",
            "driver_license_expiry_date" => "nullable|date",
            "approval_status" => "nullable|in:pending,approved,rejected",
            "driver_license_image" => "nullable|mimes:jpeg,jpg,png|max:2048",
            "permit_renewal_count" => "nullable|integer",
            "permit_number" => "nullable|string",
            "permit_expiry_date" => "nullable|string",
            "permit_image" => "nullable|mimes:jpeg,jpg,png|max:2048",
            "note" => "nullable|string",
            "street_id" => "nullable|exists:streets,id",
        ];
    }
}
