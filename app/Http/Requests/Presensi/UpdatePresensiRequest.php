<?php

namespace App\Http\Requests\Presensi;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => 'nullable|exists:siswa,id',
            'tanggal' => 'required|date',
            'kehadiran' => 'required|in:Hadir,Sakit,Izin,Alfa,Belum',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jadwal_id' => 'nullable|exists:jadwal_kbm,id',
            'mapel_id' => 'nullable|exists:mapel,id',
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.exists' => 'Siswa yang dipilih tidak ditemukan dalam database.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Tanggal harus dalam format yang valid (YYYY-MM-DD).',
            
            'kehadiran.required' => 'Status kehadiran wajib dipilih.',
            'kehadiran.in' => 'Status kehadiran harus salah satu dari: Hadir, Sakit, Izin, Alfa, Belum.',

            'kelas_id.exists' => 'Kelas yang dipilih tidak ditemukan dalam database.',
            'jadwal_id.exists' => 'Jadwal yang dipilih tidak ditemukan dalam database.',
            'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak ditemukan dalam database.',
        ];
    }
}
