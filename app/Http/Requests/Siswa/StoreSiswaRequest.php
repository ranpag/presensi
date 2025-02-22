<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'no_telp' => 'required|string|max:16|regex:/^[0-9]+$/',
            'walimurid' => 'required|string|max:100',
            'alamat' => 'required|string|max:100',
            'kelas_id' => 'nullable|exists:kelas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama siswa wajib diisi.',
            'nama.string' => 'Nama siswa harus berupa teks.',
            'nama.max' => 'Nama siswa maksimal 100 karakter.',

            'gender.required' => 'Gender wajib dipilih.',
            'gender.in' => 'Gender hanya boleh L (Laki-laki) atau P (Perempuan).',

            'nis.required' => 'NIS wajib diisi.',
            'nis.string' => 'NIS harus berupa teks.',
            'nis.max' => 'NIS maksimal 20 karakter.',
            'nis.unique' => 'NIS sudah terdaftar.',

            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'no_telp.string' => 'Nomor telepon harus berupa teks.',
            'no_telp.max' => 'Nomor telepon maksimal 16 karakter.',
            'no_telp.regex' => 'Nomor telepon hanya boleh berisi angka.',

            'walimurid.required' => 'Nama wali murid wajib diisi.',
            'walimurid.string' => 'Nama wali murid harus berupa teks.',
            'walimurid.max' => 'Nama wali murid maksimal 100 karakter.',

            'alamat.required' => 'Alamat wajib diisi.',
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
