<?php

namespace App\Http\Controllers;

use App\Models\JadwalKBM;
use App\Models\JadwalPiket;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalController extends Controller
{
    public function hari(Request $request)
    {
        // Ambil user_id dari user yang sedang login
        $user_id = auth('api')->id();

        // Pastikan user sudah login
        if (!$user_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        // Ambil hari ini (contoh: "Senin", "Selasa", dst.)
        $hari_ini = $request->has('hari') ? $request->hari : now()->locale('id')->translatedFormat('l'); // Menggunakan Carbon untuk mendapatkan nama hari

        // Ambil jadwal KBM berdasarkan hari dan user_id
        $jadwalKBM = JadwalKBM::with(['kelas.walas:id,nama', 'mapel'])
            ->where('user_id', $user_id)
            ->where('hari', $hari_ini)
            ->orderBy('mulai', 'asc')
            ->get();

        $jadwalPiket = JadwalPiket::where('user_id', $user_id)
            ->where('hari', $hari_ini)
            ->orderBy('mulai', 'asc')
            ->get();


        return response()->json([
            'success' => true,
            'message' => 'Jadwal untuk hari ini berhasil diambil.',
            'data' => [
                'kbm' => $jadwalKBM->isEmpty() ? null : $jadwalKBM,
                'piket' => $jadwalPiket->isEmpty() ? null : $jadwalPiket
            ]
        ]);
    }

    public function minggu(Request $request)
    {
        // Ambil user_id dari user yang sedang login
        $user_id = auth('api')->id();

        // Pastikan user sudah login
        if (!$user_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        // Ambil jadwal KBM selama seminggu untuk user ini, diurutkan berdasarkan hari dan waktu mulai
        $jadwalKBM = JadwalKBM::with(['kelas.walas:id,nama', 'mapel'])
            ->where('user_id', $user_id)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('mulai', 'asc')
            ->get();

        $jadwalPiket = JadwalPiket::with('guru')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('mulai', 'asc')
            ->get();

        // Format data berdasarkan hari
        $resultKbm = [];
        foreach ($jadwalKBM as $jadwal) {
            $hari = $jadwal->hari;
            $resultKbm[$hari][] = [
                'id' => $jadwal->id,
                'kelas' => $jadwal->kelas,
                'mapel' => $jadwal->mapel,
                'mulai' => $jadwal->mulai,
                'selesai' => $jadwal->selesai
            ];
        }

        $resultPiket = [];
        foreach ($jadwalPiket as $jadwal) {
            $hari = $jadwal->hari;
            $resultPiket[$hari][] = [
                'id' => $jadwal->id,
                'guru' => $jadwal->guru,
                'mulai' => $jadwal->mulai,
                'selesai' => $jadwal->selesai
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal selama seminggu berhasil diambil.',
            'data' => [
                'kbm' => $resultKbm,
                'piket' => $resultPiket
            ]
        ]);
    }

    public function download_jadwal_kbm($id)
    {
        $jadwalKBM = JadwalKBM::with(['kelas.walas:id,nama', 'mapel', 'kelas'])
            ->where('kelas_id', $id)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('mulai', 'asc')
            ->get();

        $resultKbm = [];
        foreach ($jadwalKBM as $jadwal) {
            $hari = $jadwal->hari;
            $resultKbm[$hari][] = [
                'id' => $jadwal->id,
                'kelas' => $jadwal->kelas,
                'mapel' => $jadwal->mapel,
                'mulai' => $jadwal->mulai,
                'selesai' => $jadwal->selesai
            ];
        }

        $kelas = $jadwalKBM->first()?->kelas;

        $pdf = Pdf::loadView('pdf.jadwal_kbm', [
            'jadwal' => $resultKbm,
            'kelas' => $kelas
        ]);

        return $pdf->stream('jadwal-kbm-kelas-' . $kelas->nama . '.pdf');
    }

    public function download_jadwal_piket($id)
    {
        $jadwalPiket = JadwalPiket::with('guru')
            ->where('user_id', $id)
            ->orderBy('tanggal')
            ->get();

        $pdf = Pdf::loadView('pdf.jadwal_piket', [
            'jadwal' => $jadwalPiket,
        ]);

        return $pdf->stream('jadwal-piket-user-' . $id . '.pdf');
    }

}
