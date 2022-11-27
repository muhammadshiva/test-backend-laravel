<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Rules\Password;

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
            'name' => ['required', 'string'],
            'username' => ['nullable', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'ktp' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', new Password],
            'pin' => ['required', 'min:6'],
            // 'name' => 'required|string',
            // 'username' => 'nullable|string',
            // 'email' => 'required|string|unique:users,email',
            // 'ktp' => 'nullable|string',
            // 'password' => 'required|string,new Password',
            // 'pin' => 'required|min:6'
        ];
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()]), 400);
    }
}
