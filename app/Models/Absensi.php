<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    // Nama tabel jika tidak mengikuti konvensi Laravel (opsional)
    // protected $table = 'absensis';

    // Kolom-kolom yang boleh diisi secara massal (mass assignment)
    protected $table = 'absensis';
    protected $fillable = [
        'pegawai_id',  // pastikan sesuai dengan nama kolom di tabel absensi
        'tanggal',
        'status',
        
    ];
    // Definisikan konstanta untuk status absensi
    const STATUS_HADIR = 'hadir';  

    
    protected $casts = [
        'status' => 'string',
    ];

    // Relasi ke Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id_pegawai');
    }
}
