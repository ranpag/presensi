<?php

namespace App\Http\Requests\Mapel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMapelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'string|max:50|unique:mapel,nama',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.string' => 'Nama mata pelajaran harus berupa teks.',
            'nama.max' => 'Nama mata pelajaran maksimal 50 karakter.',
            'nama.unique' => 'Nama mata pelajaran sudah terdaftar, gunakan nama lain.',
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
