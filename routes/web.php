<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RekamMedisPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get(
    '/rekam-medis/{rekamMedis}/pdf',
    [RekamMedisPdfController::class, 'generate']
)->name('rekam-medis.pdf');
