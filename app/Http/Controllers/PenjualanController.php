<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::all(); // Ambil semua data
        return view('penjualan.index', compact('penjualan'));
    }

    public function show($id)
    {
        // Ambil satu penjualan + relasi pembayarannya
        $penjualan = Penjualan::with('pembayaran')->findOrFail($id);
        return view('filament.penjualan-table', compact('penjualan'));
    }

    // Contoh di dalam controller untuk halaman pembayaran
    
public function tampilkanHalamanPembayaran($idPenjualan) // atau parameter lain
{
    $penjualan = Penjualan::with('pembayarans')->findOrFail($idPenjualan);

    // Sekarang $penjualan berisi data penjualan yang dicari beserta pembayarannya.
    // ... lanjutkan ke langkah berikutnya
}
}
