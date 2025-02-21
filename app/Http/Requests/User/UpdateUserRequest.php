<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

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
            'username' => 'string|max:50|unique:users,username',
            'email' => 'email|unique:users,email',
            'password' => [
                'string',
                Password::min(8)->letters()->numbers()->mixedCase()->symbols(),
            ],
            'role' => 'in:Admin,user',
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

            'role.in' => 'Role harus salah satu dari: Admin atau user.',
        ];
    }
}
