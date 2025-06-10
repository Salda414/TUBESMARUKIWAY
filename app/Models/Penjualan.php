<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $guarded = [];

    public static function getKodeFaktur()
    {
        $sql = "SELECT IFNULL(MAX(no_faktur), 'F-0000000') as no_faktur FROM penjualan";
        $kodefaktur = DB::select($sql);
        // cacah hasilnya
        foreach ($kodefaktur as $kdpmbl) {
            $kd = $kdpmbl->no_faktur;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd,-7);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'F-'.str_pad($noakhir,7,"0",STR_PAD_LEFT); //menyambung dengan string P-00001
        return $noakhir;
    }

    /**
     * Mendefinisikan relasi bahwa satu Penjualan dimiliki oleh satu Pelanggan.
     * Asumsi:
     * - Tabel 'penjualan' memiliki foreign key 'pelanggan_id'.
     * - Model Pelanggan ada di App\Models\Pelanggan.
     * - Primary key di tabel 'pelanggan' adalah 'id_pelanggan' (sesuai Select di Resource).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pelanggan()
    {
        // Argumen kedua adalah foreign key di tabel 'penjualan'
        // Argumen ketiga adalah owner key (primary key) di tabel 'pelanggan'
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id');
    }

    /**
     * Relasi ke item-item produk dalam penjualan melalui tabel pivot 'penjualan_barang'.
     * Ini BUKAN relasi untuk mendapatkan data pelanggan.
     * Mungkin lebih baik dinamai items() atau produkItems() untuk menghindari kebingungan.
     * Untuk saat ini, saya biarkan namanya agar tidak merusak bagian lain yang mungkin sudah menggunakannya,
     * tetapi fokus kita adalah relasi pelanggan() yang benar di atas.
     */
    // public function produkPivot() // Contoh penamaan yang berbeda
    // {
    //     return $this->belongsToMany(Produk::class, 'penjualan_barang', 'penjualan_id', 'produk_id')
    //         ->withPivot('jumlah', 'harga');
    // }


    /**
     * Relasi ke Produk (jika ada 'produk_id' langsung di tabel 'penjualan').
     * Ini biasanya kurang umum jika satu penjualan bisa banyak item.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }

    /**
     * Relasi ke Kategori (jika ada 'kategori_id' langsung di tabel 'penjualan').
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    /**
     * Relasi ke item-item barang dalam penjualan (melalui model PenjualanBarang).
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penjualanProduk()
    {
        return $this->hasMany(penjualanProduk::class, 'penjualan_id');
    }

    /**
     * Relasi ke pembayaran.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'penjualan_id'); 
    }
}