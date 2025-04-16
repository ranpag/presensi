<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:50|unique:kelas,nama',
            'tingkatan' => 'required|in:7,8,9',
            'user_id' => 'nullable|exists:users,id|unique:kelas,user_id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kelas wajib diisi.',
            'nama.string' => 'Nama kelas harus berupa teks.',
            'nama.max' => 'Nama kelas maksimal 50 karakter.',
            'nama.unique' => 'Nama kelas sudah digunakan, pilih nama lain.',

            'tingkatan.required' => 'Tingkatan kelas wajib dipilih.',
            'tingkatan.in' => 'Tingkatan harus 7, 8, atau 9.',

            'user_id.exists' => 'User tidak ditemukan dalam database.',
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
