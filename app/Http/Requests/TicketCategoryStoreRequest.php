<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketCategoryStoreRequest extends FormRequest
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
            'category_name' => 'required|string',
            'amount' => 'required|numeric',
            'category_status' => 'required|string|in:active,inactive',
            'expired_at' => 'required|date_format:H:i:s',
            'allow_multiple_ticket_purchase' => 'required|boolean',
        ];
    }
}
