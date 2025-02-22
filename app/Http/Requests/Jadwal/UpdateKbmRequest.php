<?php

namespace App\Http\Requests\Jadwal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateKbmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kelas_id' => 'nullable|exists:kelas,id',
            'mapel_id' => 'nullable|exists:mapel,id',
            'user_id' => 'nullable|exists:users,id',
            'hari' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'mulai' => 'date_format:H:i',
            'selesai' => 'date_format:H:i|after:mulai',
        ];
    }

    public function messages(): array
    {
        return [
            'kelas_id.exists' => 'Kelas yang dipilih tidak ditemukan dalam database.',
            'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak ditemukan dalam database.',
            'user_id.exists' => 'User yang dipilih tidak ditemukan dalam database.',

            'hari.in' => 'Hari harus salah satu dari: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu.',

            'mulai.date_format' => 'Format waktu mulai harus HH:MM (contoh: 08:00).',

            'selesai.date_format' => 'Format waktu selesai harus HH:MM (contoh: 16:00).',
            'selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
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
