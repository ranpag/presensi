<?php

namespace App\Http\Requests\Jadwal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePiketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'mulai' => 'required|date_format:H:i',
            'selesai' => 'required|date_format:H:i|after:mulai',
            'tanggal' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'User tidak ditemukan dalam database.',

            'mulai.required' => 'Waktu mulai wajib diisi.',
            'mulai.date_format' => 'Format waktu mulai harus HH:MM (contoh: 08:00).',

            'selesai.required' => 'Waktu selesai wajib diisi.',
            'selesai.date_format' => 'Format waktu selesai harus HH:MM (contoh: 16:00).',
            'selesai.after' => 'Waktu selesai harus setelah waktu mulai.',

            'tanggal.required' => 'Tanggal wajib dipilih.',
            'tanggal.in' => 'Tanggal harus dalam format tanggal yang valid.',
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
