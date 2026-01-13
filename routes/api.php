<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Chatify\Facades\ChatifyMessenger as Chatify;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DentalVisitController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\LayananController;
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

/*
| AUTH
*/

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,1');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:api');

/*
| PROTECTED API
*/
Route::middleware(['auth:api', 'throttle:100,1'])->group(function () {

    Route::get('/jadwal-dokter', [JadwalController::class, 'index']);
    Route::post('/reservasi', [ReservasiController::class, 'store']);
    Route::get('/reservasi', [ReservasiController::class, 'index']);
    Route::put('/reservasi/{id}/cancel', [ReservasiController::class, 'batalkan']);

    Route::get('/rekam-medis/pasien', [RekamMedisController::class, 'byPasien']);
    Route::get('/rekam-medis/reservasi/{nomorReservasi}', [RekamMedisController::class, 'byReservasi']);
    Route::get('/riwayat', [RiwayatController::class, 'index']);

    Route::get('/chat/contacts', [ChatController::class, 'contacts'])
        ->middleware('throttle:60,1');

    Route::post('/fetchMessagesMobile', [ChatController::class, 'fetchMessagesMobile'])
        ->middleware('throttle:120,1');

    Route::post('/chat/auth', function (Request $request) {
        return Chatify::pusherAuth(
            $request->user(),
            auth()->user(),
            $request->channel_name,
            $request->socket_id
        );
    })->middleware('throttle:30,1');

    Route::get('/notifications', [UserController::class, 'notifications']);
    Route::put('/notifications/{id}/read', [UserController::class, 'markNotificationRead']);
    Route::delete('/notifications/{id}', [UserController::class, 'deleteNotification']);


    // Route::put('/pasien/profile', [PasienProfileController::class, 'update']);
    // ====== PROFILE & OPTIONS ======
    Route::put('/pasien/profile', [PasienProfileController::class, 'update']);
    Route::get('/options/dokter', [OptionController::class, 'listDokter']);
    Route::get('/options/layanan', [OptionController::class, 'listLayanan']);
});

/*
| PUBLIC
*/
Route::get('/layanan', [LayananController::class, 'index'])
    ->middleware('throttle:60,1');

Route::get('/jadwal-publik', [JadwalController::class, 'index'])
    ->middleware('throttle:60,1');

Route::get('/dokter', [LayananController::class, 'getDokter'])
    ->middleware('throttle:60,1');

Route::post('/save-fcm-token', [UserController::class, 'saveFcmToken'])
    ->middleware(['auth:api', 'throttle:30,1']);
