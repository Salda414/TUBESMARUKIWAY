<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// tambahan
use Illuminate\Support\Facades\DB;

class vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor'; // Nama tabel eksplisit

    protected $guarded = []; //semua kolom boleh di isi

    public static function getKodevendor()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(kode_vendor), 'P-00000') as kode_vendor 
                FROM vendor ";
        $kodevendor = DB::select($sql);

        // cacah hasilnya
        foreach ($kodevendor as $kdpmbl) {
            $kd = $kdpmbl->kode_vendor;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd,-5);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'P-'.str_pad($noakhir,5,"0",STR_PAD_LEFT); //menyambung dengan string P-00001
        return $noakhir;

    }

    // relasi ke tabel vendor
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
        // pastikan 'user_id' adalah nama kolom foreign key
    }

    // relasi ke tabel pembelian
    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'vendor_id');
    }
}