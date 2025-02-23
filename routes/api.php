<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JadwalKBMController;
use App\Http\Controllers\JadwalPiketController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\SiswaController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.api');
    Route::get('/refresh', [AuthController::class, 'refreshToken']);
});

Route::middleware('auth.api')->group(function () {
    Route::prefix('mapel')->group(function () {
        Route::get('/', [MapelController::class, 'index']);
        Route::get('/{id}', [MapelController::class, 'show']);
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [MapelController::class, 'store']);
            Route::put('/{id}', [MapelController::class, 'update']);
            Route::patch('/{id}', [MapelController::class, 'update']);
            Route::delete('/{id}', [MapelController::class, 'destroy']);
            Route::delete('/destroy/many', [MapelController::class, 'destroyMany']);
        });
    });
    
    Route::prefix('kelas')->group(function () {
        Route::get('/', [KelasController::class, 'index']);
        Route::get('/{id}', [KelasController::class, 'show']);
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [KelasController::class, 'store']);
            Route::put('/{id}', [KelasController::class, 'update']);
            Route::patch('/{id}', [KelasController::class, 'update']);
            Route::delete('/{id}', [KelasController::class, 'destroy']);
            Route::delete('/destroy/many', [KelasController::class, 'destroyMany']);
        });
    });
    
    Route::prefix('siswa')->group(function () {
        Route::get('/', [SiswaController::class, 'index']);
        Route::get('/{id}', [SiswaController::class, 'show']);
    
        Route::middleware('admin')->group(function () {
            Route::post('/', [SiswaController::class, 'store']);
            Route::put('/{id}', [SiswaController::class, 'update']);
            Route::patch('/{id}', [SiswaController::class, 'update']);
            Route::delete('/{id}', [SiswaController::class, 'destroy']);
            Route::delete('/destroy/many', [SiswaController::class, 'destroyMany']);
        });
    });
    
    
    Route::prefix('jadwal')->group(function () {
        Route::get('/hari', [JadwalController::class, 'hari']);
        Route::get('/minggu', [JadwalController::class, 'minggu']);

        Route::prefix('kbm')->group(function () {
            Route::get('/', [JadwalKBMController::class, 'index']);
            Route::get('/{id}', [JadwalKBMController::class, 'show']);
    
            Route::middleware('admin')->group(function () {
                Route::post('/', [JadwalKBMController::class, 'store']);
                Route::put('/{id}', [JadwalKBMController::class, 'update']);
                Route::patch('/{id}', [JadwalKBMController::class, 'update']);
                Route::delete('/{id}', [JadwalKBMController::class, 'destroy']);
                Route::delete('/destroy/many', [JadwalKBMController::class, 'destroyMany']);        
            });
        });
    
        Route::prefix('piket')->group(function () {
            Route::get('/', [JadwalPiketController::class, 'index']);
            Route::get('/{id}', [JadwalPiketController::class, 'show']);
    
            Route::middleware('admin')->group(function () {
                Route::post('/', [JadwalPiketController::class, 'store']);
                Route::put('/{id}', [JadwalPiketController::class, 'update']);
                Route::patch('/{id}', [JadwalPiketController::class, 'update']);
                Route::delete('/{id}', [JadwalPiketController::class, 'destroy']);
                Route::delete('/destroy/many', [JadwalPiketController::class, 'destroyMany']);        
            });
        });
    });
});