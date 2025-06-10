<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanBarang extends Model
{
    use HasFactory;

    protected $table = 'penjualan_barang';
    protected $fillable = ['penjualan_id', 'produk_id', 'harga_beli', 'harga_jual', 'jumlah', 'harga', 'no_faktur', 'updated_at', 'created_at' ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function produk()
{
    return $this->belongsTo(Produk::class, 'produk_id');
}
}