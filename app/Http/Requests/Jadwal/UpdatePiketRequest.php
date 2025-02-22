<?php

namespace App\Http\Requests\Jadwal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePiketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'mulai' => 'date_format:H:i',
            'selesai' => 'date_format:H:i|after:mulai',
            'hari' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'User tidak ditemukan dalam database.',

            'mulai.date_format' => 'Format waktu mulai harus HH:MM (contoh: 08:00).',

            'selesai.date_format' => 'Format waktu selesai harus HH:MM (contoh: 16:00).',
            'selesai.after' => 'Waktu selesai harus setelah waktu mulai.',

            'hari.in' => 'Hari harus salah satu dari: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu.',
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
