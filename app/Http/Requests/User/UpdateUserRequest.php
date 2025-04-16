<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'string|max:100',
            'username' => 'string|max:50|unique:users,username,' . $this->route('id'),
            'email' => 'email|unique:users,email,' . $this->route('id'),
            'password' => [
		'nullable',
                'string',
                Password::min(8)->letters()->numbers()->mixedCase()->symbols(),
            ],
            'role' => 'in:admin,user',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 100 karakter.',

            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.unique' => 'Username sudah digunakan.',

            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',

            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.letters' => 'Password harus mengandung setidaknya satu huruf.',
            'password.numbers' => 'Password harus mengandung setidaknya satu angka.',
            'password.mixedCase' => 'Password harus memiliki huruf besar dan kecil.',
            'password.symbols' => 'Password harus mengandung setidaknya satu simbol.',

            'role.in' => 'Role harus salah satu dari: admin atau user.',
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
