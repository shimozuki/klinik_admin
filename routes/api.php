<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DentalVisitController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\OptionController;
use App\Http\Controllers\Api\PasienProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ReservasiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RekamMedisController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('/jadwal-dokter', [JadwalController::class, 'index']);
    Route::post('/reservasi', [ReservasiController::class, 'store']);
    Route::get('/reservasi', [ReservasiController::class, 'index']);
    Route::put('/reservasi/{id}/cancel', [ReservasiController::class, 'batalkan']);



    Route::get('/rekam-medis/pasien', [RekamMedisController::class, 'byPasien']);
    Route::get('/rekam-medis/reservasi/{nomorReservasi}', [RekamMedisController::class, 'byReservasi']);

    Route::get('/Dental-visit', [DentalVisitController::class, 'index']);



    Route::put('/pasien/profile', [PasienProfileController::class, 'update']);

    Route::get('/options/dokter', [OptionController::class, 'listDokter']);
    Route::get('/options/layanan', [OptionController::class, 'listLayanan']);
});
