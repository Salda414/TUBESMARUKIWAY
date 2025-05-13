<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianBahanBaku extends Model
{
    protected $table = 'pembelian_bahan_baku';

    protected $fillable = [
        'nama_produk', // diinput manual
        'vendor_id',
        'jumlah',
        'harga_satuan',
        'tanggal_pembelian',
    ];

    // Hapus relasi produk karena sudah tidak digunakan
    // public function produk() { ... }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
