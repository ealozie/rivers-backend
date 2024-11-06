<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceSubCategoryRequestStore extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'service_category_id' => ['required',],
            'service_provider_id' => ['required'],
            'fees' => ['required', 'string'],
            'require_login' => 'nullable|boolean',
            'processing_time' => ['required', 'string'],
            'status' => 'nullable',
            'landing_page_url' => ['required', 'string'],
        ];
    }
}
