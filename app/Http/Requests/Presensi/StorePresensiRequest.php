<?php

namespace App\Http\Requests\Presensi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePresensiRequest extends FormRequest
{
     public function authorize()
    {
        return true; // Pastikan diatur ke true agar request bisa dieksekusi
    }

    public function rules()
    {
        return [
            '*.presensi_id' => 'required|exists:presensi,id',
            '*.status' => 'required|in:Hadir,Sakit,Izin,Alfa',
        ];
    }

    public function messages()
    {
        return [
            '*.presensi_id.required' => 'ID presensi harus diisi.',
            '*.presensi_id.exists' => 'Presensi tidak ditemukan.',
            '*.status.required' => 'Status presensi harus diisi.',
            '*.status.in' => 'Status hanya boleh: Hadir, Sakit, Izin, atau Alfa.',
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
