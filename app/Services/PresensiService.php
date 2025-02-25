<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\JadwalKBM;
use Illuminate\Support\Facades\DB;

class PresensiService
{
    public function generatePresensi()
    {
        $hariIni = Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l'); // Hari ini (Senin, Selasa, dll.)
        $tanggalHariIni = Carbon::today('Asia/Jakarta')->toDateString();

        // Cek apakah hari ini adalah tanggal merah
        $isTanggalMerah = DB::table('tanggal_merah')
            ->whereDate('mulai', '<=', $tanggalHariIni)
            ->whereDate('selesai', '>=', $tanggalHariIni)
            ->exists();

        if ($isTanggalMerah || $hariIni === 'Minggu') {
            return false; // Tidak membuat presensi jika hari ini adalah tanggal merah atau hari Minggu
        }

        $jadwalHariIni = JadwalKBM::where('hari', $hariIni)->get(['id', 'kelas_id', 'mapel_id']);
        if ($jadwalHariIni->isEmpty()) return false;

        // Ambil semua siswa yang kelasnya sesuai dengan jadwal
        $kelasIds = $jadwalHariIni->pluck('kelas_id')->unique();
        $siswa = Siswa::whereIn('kelas_id', $kelasIds)->get(['id', 'kelas_id']);
        if ($siswa->isEmpty()) return false;

        $tanggalHariIni = Carbon::today('Asia/Jakarta')->toDateString();

        // Ambil semua presensi yang sudah ada hari ini untuk lookup cepat
        $presensiExist = Presensi::where('tanggal', $tanggalHariIni)
            ->get(['siswa_id', 'jadwal_id'])
            ->mapWithKeys(fn ($p) => ["{$p->siswa_id}_{$p->jadwal_id}" => true])
            ->toArray();

        $presensiData = [];

        foreach ($jadwalHariIni as $jadwal) {
            foreach ($siswa as $s) {
                if ($s->kelas_id !== $jadwal->kelas_id) continue; // Skip jika kelas tidak cocok

                $key = "{$s->id}_{$jadwal->id}";
                if (!isset($presensiExist[$key])) { // Jika belum ada, tambahkan ke batch insert
                    $presensiData[] = [
                        'siswa_id'   => $s->id,
                        'tanggal'    => $tanggalHariIni,
                        'kehadiran'  => null,
                        'kelas_id'   => $jadwal->kelas_id,
                        'jadwal_id'  => $jadwal->id,
                        'mapel_id'   => $jadwal->mapel_id,
                        'created_at' => now('Asia/Jakarta'),
                        'updated_at' => now('Asia/Jakarta'),
                    ];
                }
            }
        }

        if (!empty($presensiData)) {
            DB::transaction(fn () => DB::table('presensi')->insert($presensiData));
            return true;
        }

        return false;
    }

    public static function getPresensiSaatIni($userId)
    {
        $hariIni = Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l'); // Contoh: 'Senin'
        $waktuSekarang = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // Cari jadwal yang sedang berlangsung sekarang
        $jadwalSaatIni = JadwalKbm::where('hari', $hariIni)
            ->where('mulai', '<=', $waktuSekarang)
            ->where('selesai', '>=', $waktuSekarang)
            ->where('user_id', $userId)
            ->first();

        // Jika tidak ada jadwal yang cocok, return koleksi kosong
        if (!$jadwalSaatIni) {
            return collect();
        }

        // Ambil presensi berdasarkan jadwal yang sedang aktif
        return Presensi::where('jadwal_id', $jadwalSaatIni->id)
            ->with(['siswa:id,nama', 'jadwal']) // Jika ada relasi siswa & jadwal
            ->get();
    }

    public static function getPresensiHariIni($userId)
    {
        $hariIni = Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l'); // Contoh: 'Senin'

        // Cari jadwal yang sesuai dengan hari ini dan user_id = 5
        $jadwalSaatIni = JadwalKbm::where('hari', $hariIni)
            ->where('user_id', $userId)
            ->get(['id', 'kelas_id']);

        // Jika tidak ada jadwal yang cocok, return koleksi kosong
        if ($jadwalSaatIni->isEmpty()) return collect();

        // Ambil semua presensi berdasarkan jadwal yang ditemukan
        $presensi = Presensi::whereIn('jadwal_id', $jadwalSaatIni->pluck('id'))
            ->with(['siswa:id,nama', 'jadwal', 'kelas:id,nama'])
            ->get();

        // Format data sesuai permintaan
        $result = $jadwalSaatIni->map(function ($jadwal) use ($presensi) {
            return [
                'kelas' => [
                    'id' => $jadwal->kelas_id,
                    'nama' => optional($jadwal->kelas)->nama,
                ],
                'presensi' => $presensi->where('jadwal_id', $jadwal->id)->values()
            ];
        });

        return $result;
    }
}


