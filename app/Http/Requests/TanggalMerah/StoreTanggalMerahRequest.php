<?php

namespace App\Http\Requests\TanggalMerah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTanggalMerahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mulai' => 'required|date',
            'selesai' => 'required|date|after_or_equal:mulai',
            'deskripsi' => 'string|min:5|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'mulai.required' => 'Tanggal mulai wajib diisi.',
            'mulai.date' => 'Tanggal mulai harus dalam format tanggal yang valid.',
            'selesai.required' => 'Tanggal selesai wajib diisi.',
            'selesai.date' => 'Tanggal selesai harus dalam format tanggal yang valid.',
            'selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'deskripsi.min' => 'Deskripsi minimal terdiri dari 5 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal terdiri dari 255 karakter.',
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
