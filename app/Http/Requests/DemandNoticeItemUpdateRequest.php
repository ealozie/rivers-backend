<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandNoticeItemUpdateRequest extends FormRequest
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
            'demand_notice_id' => 'sometimes|exists:demand_notices,id',
            'year_id' => 'sometimes|exists:assessment_years,id',
            'revenue_item_id' => 'sometimes|exists:revenue_items,id',
            'amount' => 'sometimes|numeric|min:0',
            'payment_status' => 'sometimes|in:pending,paid,partial,overdue',
            'payment_receipt_number' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'demand_notice_id.exists' => 'The selected demand notice does not exist.',
            'year_id.exists' => 'The selected assessment year does not exist.',
            'revenue_item_id.exists' => 'The selected revenue item does not exist.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'payment_status.in' => 'The payment status must be one of: pending, paid, partial, overdue.',
        ];
    }
}
