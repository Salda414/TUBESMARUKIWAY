<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//relasi dengan produk
use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    /** @use HasFactory<\Database\Factories\PelangganFactory> */
    use HasFactory;
    protected $table = 'pelanggan'; // Nama tabel eksplisit
    protected $primaryKey = 'id_pelanggan'; // Tentukan primary key
    protected $fillable = ['nama_pelanggan','produk_id', 'nomor_telepon', 'email', 'alamat',];
    protected $guarded = [];

    // app/Models/Pelanggan.php
    public function produk()
    {
    return $this->belongsTo(Produk::class, 'produk_id');
    }
    // relasi ke tabel pelanggan
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
        // pastikan 'user_id' adalah nama kolom foreign key
    }

    // relasi ke tabel penjualan
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'pelanggan_id');
    }
}
