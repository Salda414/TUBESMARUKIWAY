<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// tambahan
use Illuminate\Support\Facades\DB;

class Pelanggan extends Model
{
    /** @use HasFactory<\Database\Factories\PelangganFactory> */
    use HasFactory;
    protected $table = 'pelanggan'; // Nama tabel eksplisit
    protected $guarded = []; //semua kolom boleh di isi
    
    public static function getKodePembeli()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(kode_pelanggan), 'P-00000') as kode_pelanggan 
                FROM pelanggan ";
        $kodepelanggan = DB::select($sql);

        // cacah hasilnya
        foreach ($kodepelanggan as $kdpmbl) {
            $kd = $kdpmbl->kode_pelanggan;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = (int) substr($kd,-5);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'P-'.str_pad($noakhir,5,"0",STR_PAD_LEFT); //menyambung dengan string P-00001
        return $noakhir;

    }

    // relasi ke tabel pembeli
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

    protected $fillable = [
        'name',
        'email',
        'user_id',  // <-- tambahkan ini supaya bisa diisi massal
        // field lain yang diizinkan
    ];

}
