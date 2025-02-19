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
            'user_id' => 'required|exists:users,id',
            'plate_number' => 'required|string',
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'vehicle_manufacturer_id' => 'required|exists:vehicle_manufacturers,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'chassis_number' => 'required',
            'engine_number' => 'required',
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'capacity' => 'sometimes|string',
            'routes' => 'sometimes|string',
            'driver_id' => 'sometimes|exists:users,id',
            'driver_license_number' => 'sometimes|string',
            'driver_license_expiry_date' => 'sometimes|date',
            'driver_license_image' => 'sometimes|mimes:jpeg,jpg,png|max:2048',
            'permit_renewal_count' => 'sometimes|integer',
            'permit_number' => 'sometimes|string',
            'permit_expiry_date' => 'sometimes|string',
            'permit_image' => 'sometimes|mimes:jpeg,jpg,png|max:2048',
            'note' => 'sometimes|string',
            'street_id' => 'nullable|exists:streets,id',
        ];
    }
}
