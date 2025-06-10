<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Contoh1Controller;
use App\Http\Controllers\Contoh2Controller;
use App\Http\Controllers\CoaController;
use Illuminate\Support\Facades\Auth;

// Route utama
Route::get('/', function () {
    return view('login'); // Mengarahkan ke login customer
});

// Route untuk halaman contoh
Route::get('/selamat', function () {
    return view('selamat', [
        'nama' => 'Putri Valina',
        'nim' => '113030044'
    ]);
});

Route::get('/utama', function () {
    return view('layout', [
        'nama' => 'Putri Valina',
        'title' => 'Selamat Datang di Matakuliah Web Framework'
    ]);
});

// Route contoh controller
Route::get('/contoh1', [Contoh1Controller::class, 'show']);
Route::get('/contoh2', [Contoh2Controller::class, 'show']);
Route::get('/coa', [CoaController::class, 'index']);

// Login dan logout route
Route::get('/login', function () {
    return view('login');
});

Route::post('/login', [AuthController::class, 'login']); // Proses login

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Route untuk ubah password
Route::get('/ubahpassword', [AuthController::class, 'ubahpassword'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class)
    ->name('ubahpassword');

Route::post('/prosesubahpassword', [AuthController::class, 'prosesubahpassword'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class);

// Route untuk keranjang
Route::post('/tambah', [KeranjangController::class, 'tambahKeranjang'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class);

Route::get('/lihatkeranjang', [KeranjangController::class, 'lihatkeranjang'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class);

Route::delete('/hapus/{barang_id}', [KeranjangController::class, 'hapus'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class);

Route::get('/lihatriwayat', [KeranjangController::class, 'lihatriwayat'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class);

// Route untuk cek status pembayaran
Route::get('/cek_status_pembayaran_pg', [KeranjangController::class, 'cek_status_pembayaran_pg']);

// Route untuk perusahaan
Route::resource('perusahaan', PerusahaanController::class);
Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class, 'destroy']);

// Route untuk penjualan
Route::get('/penjualan', [PenjualanController::class, 'index']);
Route::get('/penjualan/{id}', [PenjualanController::class, 'show']);

