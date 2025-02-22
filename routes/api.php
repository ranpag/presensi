<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth.api');
Route::get('/auth/refresh', [AuthController::class, 'refreshToken']);

// Route::group(['middleware' => 'auth.api'], function () {
    
// });