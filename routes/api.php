<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Chatify\Facades\ChatifyMessenger as Chatify;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DentalVisitController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\OptionController;
use App\Http\Controllers\Api\PasienProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Api\RekamMedisController;
use App\Http\Controllers\Api\RiwayatController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| PROTECTED API (JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // ====== CORE ======
    Route::get('/jadwal-dokter', [JadwalController::class, 'index']);
    Route::post('/reservasi', [ReservasiController::class, 'store']);
    Route::get('/reservasi', [ReservasiController::class, 'index']);
    Route::put('/reservasi/{id}/cancel', [ReservasiController::class, 'batalkan']);

    Route::get('/rekam-medis/pasien', [RekamMedisController::class, 'byPasien']);
    Route::get('/rekam-medis/reservasi/{nomorReservasi}', [RekamMedisController::class, 'byReservasi']);
    Route::get('/riwayat', [RiwayatController::class, 'index']);

    // ====== CHAT (HTTP) ======
    Route::get('/chat/contacts', [ChatController::class, 'contacts']);
    Route::post('/fetchMessagesMobile', [ChatController::class, 'fetchMessagesMobile']);

    // ====== CHAT (PUSHER AUTH) ======
    Route::post('/chat/auth', function (Request $request) {
        return Chatify::pusherAuth(
            $request->user(),        // JWT user
            auth()->user(),          // same user
            $request->channel_name,
            $request->socket_id
        );
    });

    // ====== PROFILE & OPTIONS ======
    Route::put('/pasien/profile', [PasienProfileController::class, 'update']);
    Route::get('/options/dokter', [OptionController::class, 'listDokter']);
    Route::get('/options/layanan', [OptionController::class, 'listLayanan']);
});

Route::post('/save-fcm-token', [UserController::class, 'saveFcmToken'])
    ->middleware('auth:api');
