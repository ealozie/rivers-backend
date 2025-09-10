<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandNoticeItemStoreRequest extends FormRequest
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
            'demand_notice_id' => 'required|exists:demand_notices,id',
            'year_id' => 'required|exists:assessment_years,id',
            'revenue_item_id' => 'required|exists:revenue_items,id',
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'nullable|in:pending,paid,partial,overdue',
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
            'demand_notice_id.required' => 'The demand notice is required.',
            'demand_notice_id.exists' => 'The selected demand notice does not exist.',
            'year_id.required' => 'The assessment year is required.',
            'year_id.exists' => 'The selected assessment year does not exist.',
            'revenue_item_id.required' => 'The revenue item is required.',
            'revenue_item_id.exists' => 'The selected revenue item does not exist.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'payment_status.in' => 'The payment status must be one of: pending, paid, partial, overdue.',
        ];
    }
}
