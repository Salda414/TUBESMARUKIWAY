<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
<<<<<<< main
=======

// route ke utama
Route::get('/utama', function () {
    return view('layout',
                 [
                    'nama'=>'Putri Valina',
                    'title'=>'Selamat Datang di Matakuliah Web Framework'
                 ]
                );
});

// contoh route dengan mengakses method show di class contoh1controller
Route::get('/contoh1', [App\Http\Controllers\Contoh1Controller::class, 'show']);

Route::get('/contoh2', [App\Http\Controllers\Contoh2Controller::class, 'show']);
Route::get('/coa', [App\Http\Controllers\CoaController::class, 'index']);

// login customer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class, 'daftarproduk'])
     ->middleware(middleware:\App\Http\Middleware\CustomerMiddleware::class)
     ->name('depan');
Route::get('/login', function () {
    return view('login');
});

// tambahan route untuk proses login
use Illuminate\Http\Request;
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// untuk ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class)
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class)
;
// prosesubahpassword
// tambah keranjang
Route::post('/tambah', [App\Http\Controllers\KeranjangController::class, 'tambahKeranjang'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
Route::get('/lihatkeranjang', [App\Http\Controllers\KeranjangController::class, 'lihatkeranjang'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
Route::delete('/hapus/{produk_id}', [App\Http\Controllers\KeranjangController::class, 'hapus'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
Route::get('/lihatriwayat', [App\Http\Controllers\KeranjangController::class, 'lihatriwayat'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
// untuk autorefresh
Route::get('/cek_status_pembayaran_pg', [App\Http\Controllers\KeranjangController::class, 'cek_status_pembayaran_pg']);
Route::get('/login', function () {
    return view('login');
});

// untuk contoh perusahaan
use App\Http\Controllers\PerusahaanController;
Route::resource('perusahaan', PerusahaanController::class);
Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class,'destroy']);

// contoh sampel midtrans
use App\Http\Controllers\CobaMidtransController;
Route::get('/cekmidtrans', [CobaMidtransController::class, 'cekmidtrans']);
>>>>>>> local
