<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model {
    use HasFactory;

    protected $table = 'produk'; // Menyesuaikan dengan nama tabel di database
    protected $fillable = ['nama_produk', 'deskripsi', 'harga', 'stok', 'status', 'kategori_id'];

    public function kategori() {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
    public function pelanggan()
{
    return $this->hasMany(Pelanggan::class, 'produk_id');
}

}

