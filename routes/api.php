<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('/jadwal-dokter', [\App\Http\Controllers\Api\JadwalController::class, 'index']);
});
