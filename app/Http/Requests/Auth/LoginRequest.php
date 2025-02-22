<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'sometimes|required_without:email|string',
            'email' => 'sometimes|required_without:username|email',
            'password' => [
                'required',
                'string',
                Password::min(8)->letters()->numbers()->mixedCase()->symbols(),
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'username.required_without' => 'Username atau email harus diisi.',
            'username.string' => 'Username harus berupa teks.',

            'email.required_without' => 'Email atau username harus diisi.',
            'email.email' => 'Format email tidak valid.',

            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.letters' => 'Password harus mengandung setidaknya satu huruf.',
            'password.numbers' => 'Password harus mengandung setidaknya satu angka.',
            'password.mixedCase' => 'Password harus memiliki huruf besar dan kecil.',
            'password.symbols' => 'Password harus mengandung setidaknya satu simbol.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation Error',
            'errors' => $validator->errors()
        ], 400));
    }
}

