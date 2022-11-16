<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'ktp' => 'required|string',
            'password' => 'required|string',
            'pin' => 'required|min:6'
        ];
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException( response()->json(['errors' => $validator->errors()]), 400);
    }
}
