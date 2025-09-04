<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandNoticeCategoryItemStoreRequest extends FormRequest
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
            'demand_notice_category_id' => 'required|exists:demand_notice_categories,id',
            'revenue_item_id' => 'required|exists:revenue_items,id',
            'amount' => 'required',
        ];
    }
}
