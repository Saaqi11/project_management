<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaintainerRequest extends FormRequest
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
        $userId = isset($this->user_id) ? $this->user_id : null;
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email,' . $userId,
            'contact_number' => 'bail|required|numeric|unique:users,contact_number,' . $userId,
            'password' => (is_null($userId)) ? 'required|min:6' : 'nullable',
            'property_id' => 'required',
        ];
        return $rules;
    }
}
