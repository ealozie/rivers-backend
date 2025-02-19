<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndividualRelativeUpdateRequest extends FormRequest
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
            'entity_id' => 'nullable|exists:individuals,individual_id',
            'relative_id' => 'nullable|exists:individuals,individual_id',
            'relationship' => 'nullable|string|in:wife,husband,brother,siter,father,mother,cousin,newphew,son,daughter,grand_father,grand_mother',
        ];
    }
}
