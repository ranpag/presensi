<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JadwalKBMController;
use App\Http\Controllers\JadwalPiketController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TanggalMerahController;
use App\Http\Controllers\UserController;

Route::get('/siswa/{id}/sanksi/surat', [SiswaController::class, 'buat_surat_siswa']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.api');
    Route::get('/refresh', [AuthController::class, 'refreshToken']);
});

Route::middleware('auth.api')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::patch('/{id}', [UserController::class, 'update']);
            Route::delete('/destroy/many', [UserController::class, 'destroyMany']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });
    });

    Route::prefix('mapel')->group(function () {
        Route::get('/', [MapelController::class, 'index']);
        Route::get('/{id}', [MapelController::class, 'show']);
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [MapelController::class, 'store']);
            Route::put('/{id}', [MapelController::class, 'update']);
            Route::patch('/{id}', [MapelController::class, 'update']);
            Route::delete('/destroy/many', [MapelController::class, 'destroyMany']);
            Route::delete('/{id}', [MapelController::class, 'destroy']);
        });
    });
    
    Route::prefix('kelas')->group(function () {
        Route::get('/', [KelasController::class, 'index']);
        Route::get('/{id}', [KelasController::class, 'show']);
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [KelasController::class, 'store']);
            Route::put('/{id}', [KelasController::class, 'update']);
            Route::patch('/{id}', [KelasController::class, 'update']);
            Route::delete('/destroy/many', [KelasController::class, 'destroyMany']);
            Route::delete('/{id}', [KelasController::class, 'destroy']);
        });
    });
    
    Route::prefix('siswa')->group(function () {
        Route::get('/', [SiswaController::class, 'index']);
        Route::get('/kelas', [SiswaController::class, 'siswa_kelas_saya']); // Siswa sesuai wali kelas
        Route::get('/kelas/terperingat', [SiswaController::class, 'siswa_kelas_terperingat']); // Siswa sesuai wali kelas dan yang stack alfanya lebih dari 3
        Route::get('/{id}', [SiswaController::class, 'show']);
         // Siswa yang akan di download suratnya
        Route::get('/{id}/sanksi/selesai', [SiswaController::class, 'buat_surat_siswa']); // Siswa akan tereset stack alfanya
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [SiswaController::class, 'store']);
            Route::put('/{id}', [SiswaController::class, 'update']);
            Route::patch('/{id}', [SiswaController::class, 'update']);
            Route::delete('/destroy/many', [SiswaController::class, 'destroyMany']);
            Route::delete('/{id}', [SiswaController::class, 'destroy']);
        });
    });

    Route::prefix('tanggal-merah')->group(function () {
        Route::get('/', [TanggalMerahController::class, 'index']);
        Route::get('/{id}', [TanggalMerahController::class, 'show']);
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [TanggalMerahController::class, 'store']);
            Route::put('/{id}', [TanggalMerahController::class, 'update']);
            Route::patch('/{id}', [TanggalMerahController::class, 'update']);
            Route::delete('/destroy/many', [TanggalMerahController::class, 'destroyMany']);
            Route::delete('/{id}', [TanggalMerahController::class, 'destroy']);
        });
    });
    
    
    Route::prefix('jadwal')->group(function () {
        Route::get('/hari-ini', [JadwalController::class, 'hari']);
        Route::get('/minggu-ini', [JadwalController::class, 'minggu']);

        Route::prefix('kbm')->group(function () {
            Route::get('/', [JadwalKBMController::class, 'index']);
            Route::get('/{id}', [JadwalKBMController::class, 'show']);
    
            Route::middleware('admin')->group(function () {
                Route::post('/', [JadwalKBMController::class, 'store']);
                Route::put('/{id}', [JadwalKBMController::class, 'update']);
                Route::patch('/{id}', [JadwalKBMController::class, 'update']);
                Route::delete('/destroy/many', [JadwalKBMController::class, 'destroyMany']);        
                Route::delete('/{id}', [JadwalKBMController::class, 'destroy']);
            });
        });
    
        Route::prefix('piket')->group(function () {
            Route::get('/', [JadwalPiketController::class, 'index']);
            Route::get('/{id}', [JadwalPiketController::class, 'show']);
    
            Route::middleware('admin')->group(function () {
                Route::post('/', [JadwalPiketController::class, 'store']);
                Route::put('/{id}', [JadwalPiketController::class, 'update']);
                Route::patch('/{id}', [JadwalPiketController::class, 'update']);
                Route::delete('/destroy/many', [JadwalPiketController::class, 'destroyMany']);        
                Route::delete('/{id}', [JadwalPiketController::class, 'destroy']);
            });
        });
    });

    Route::prefix('presensi')->group(function () {
        Route::get('/saat-ini', [PresensiController::class, 'saat_ini']);
        Route::get('/hari-ini', [PresensiController::class, 'hari_ini']);
        Route::get('/alfa', [PresensiController::class, 'alfa']);
        // Route::get('/rekap', [PresensiController::class, 'hari_ini']);

        Route::get('/', [PresensiController::class, 'index']);
        Route::get('/{id}', [PresensiController::class, 'show']);

        Route::post('/', [PresensiController::class, 'store']);
        
        Route::middleware('admin')->group(function () {
            Route::patch('/{id}', [PresensiController::class, 'update']);
            Route::put('/{id}', [PresensiController::class, 'update']);
            Route::delete('/destroy/many', [PresensiController::class, 'destroyMany']);
            Route::delete('/{id}', [PresensiController::class, 'destroy']);
        });
    });
});