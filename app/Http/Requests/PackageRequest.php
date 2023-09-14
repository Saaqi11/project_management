<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
        return [
            'name' => 'required|unique:packages,name,' . $this->id,
            'max_property' => 'required|integer',
            'max_tenant' => 'required|integer',
            'status' => 'required|integer',
            'monthly_price' => 'required|numeric',
            'yearly_price' => 'required|numeric',
        ];
    }
}
