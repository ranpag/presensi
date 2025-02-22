<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'string|max:100',
            'gender' => 'in:L,P',
            'nis' => 'string|max:20|unique:siswa,nis',
            'no_telp' => 'string|max:16|regex:/^[0-9]+$/',
            'walimurid' => 'string|max:100',
            'alamat' => 'string|max:100',
            'kelas_id' => 'nullable|exists:kelas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.string' => 'Nama siswa harus berupa teks.',
            'nama.max' => 'Nama siswa maksimal 100 karakter.',

            'gender.in' => 'Gender hanya boleh L (Laki-laki) atau P (Perempuan).',

            'nis.string' => 'NIS harus berupa teks.',
            'nis.max' => 'NIS maksimal 20 karakter.',
            'nis.unique' => 'NIS sudah terdaftar.',

            'no_telp.string' => 'Nomor telepon harus berupa teks.',
            'no_telp.max' => 'Nomor telepon maksimal 16 karakter.',
            'no_telp.regex' => 'Nomor telepon hanya boleh berisi angka.',

            'walimurid.string' => 'Nama wali murid harus berupa teks.',
            'walimurid.max' => 'Nama wali murid maksimal 100 karakter.',

            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat maksimal 100 karakter.',

            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
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
