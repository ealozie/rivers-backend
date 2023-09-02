<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndividualStoreRequest extends FormRequest
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
        /*
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bvn')->nullable();
            $table->string('nin')->nullable();
            $table->string('tin')->nullable();
            $table->foreignId('nationality_id');
            $table->foreignId('title_id');
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('gender');
            $table->foreignId('marital_status_id');
            $table->integer('number_of_kids')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('blood_group_id');
            $table->foreignId('geno_type_id');
            $table->foreignId('state_id');
            $table->foreignId('local_government_area_id');
            $table->foreignId('occupation_id');
            $table->double('income_range')->nullable();
            $table->foreignId('demand_notice_category_id');
            $table->string('property_abssin')->nullable();
*/
        return [
            'bvn' => 'sometime|string',
            'nin' => 'sometime|string',
            'tin' => 'sometime|string',
            'nationality_id' => 'required|exists:nationalities,id',
            'title_id' => 'required|exists:titles,id',
            'surname' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'gender' => 'required|string',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'number_of_kids' => 'required|integer',
            'date_of_birth' => 'required|date',
            'blood_group_id' => 'required|exists:blood_groups,id',
            'geno_type_id' => 'required|exists:geno_types,id',
            'state_id' => 'required|exists:states,id',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
            'occupation_id' => 'required|exists:occupations,id',
            'income_range' => 'required|double',
            'demand_notice_category_id' => 'required|exists:demand_notice_categories,id',
            'property_abssin' => 'required|string',


        ];
    }
}
