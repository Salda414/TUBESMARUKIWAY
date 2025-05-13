<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengirimanEmailController;

Route::prefix('admin')->group(function () {
    Route::get('/penggajian/kirim/{id}', [PengirimanEmailController::class, 'kirim'])
        ->name('penggajian.kirim');
});

Route::get('/', function () {
    return view('welcome');
});
