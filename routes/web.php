<?php

use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JadwalKBMController;
use App\Http\Controllers\PresensiController;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/jadwal/{id}', [JadwalKBMController::class, 'show']);

Route::get('/pdf/jadwal-kbm/{id}', [JadwalController::class, 'show_kbm_html'])->name('jadwal.html');
Route::get('/pdf/jadwal-piket/{id}', [JadwalController::class, 'show_piket_html'])->name('piket.html');
Route::get('/pdf/rekap/{id}', [PresensiController::class, 'show_rekap'])->name('jadwal.html');

Route::get('/pdf/3hari/{id}', function ($id) {
    $siswa = Siswa::with(['kelas.walas', 'stackAlfaMapel' => function ($query) {
        $query->where('stack_alfa', '>=', 3);
    }])
        ->where(function ($query) use ($id) {
            $query
                ->where('id', $id)
                ->where('stack_alfa_hari', '>=', 3)
                ->orWhereHas('stackAlfaMapel', function ($query) {
                    $query->where('stack_alfa', '>=', 3);
                });
        })
        ->first();

    if (!$siswa) {
        return response()->json([
            'success' => false,
            'message' => 'Data siswa tidak ditemukan.',
        ], 404);
    }

    $pdf = Pdf::loadView('pdf.3hari', ['siswa' => $siswa]);
    return $pdf->stream('surat-teguran.pdf');
});

Route::get('/pdf/3mapel/{id}', function ($id) {
    $siswa = Siswa::with(['kelas.walas', 'stackAlfaMapel' => function ($query) {
        $query->where('stack_alfa', '>=', 3);
    }])
        ->where(function ($query) use ($id) {
            $query
                ->where('id', $id)
                ->where('stack_alfa_hari', '>=', 3)
                ->orWhereHas('stackAlfaMapel', function ($query) {
                    $query->where('stack_alfa', '>=', 3);
                });
        })
        ->first();

    if (!$siswa) {
        return response()->json([
            'success' => false,
            'message' => 'Data siswa tidak ditemukan.',
        ], 404);
    }

    $pdf = Pdf::loadView('pdf.3mapel', ['siswa' => $siswa]);
    return $pdf->stream('surat-teguran.pdf');
});

Route::get('/pdf/presensi-kosong/{kelasId}', [PresensiController::class, 'show_presensiKosong'])->name('presensi.kosong');
