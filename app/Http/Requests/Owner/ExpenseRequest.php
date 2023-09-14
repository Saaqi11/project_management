<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules =  [
            'name' => 'required|min:2|max:255',
            'property_id' => 'required',
            'property_unit_id' => 'required',
            'expense_type_id' => 'required',
            'description' => 'required',
            'total_amount' => 'required|numeric|min:0',
            'responsibilities' => 'required|array',
        ];

        return $rules;
    }
}
