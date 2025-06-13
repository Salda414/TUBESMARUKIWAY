<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    //
    protected $table = 'penggajians';
    protected $fillable = [
        'no_penggajian',
        'pegawai_id',
        'jumlah_hadir',
        'gaji_per_hari',
        'total_gaji',
        'status_pembayaran',
        'periode_awal',
        'periode_akhir',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id_pegawai');
    }
    // Model Penggajian
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'pegawai_id');
    }

    // Di model Penggajian
    public function hitungJumlahHadir($pegawaiId, $periodeAwal, $periodeAkhir)
    {
        return \App\Models\Absensi::where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir])
            ->count();
    }


    protected static function booted()
    {
    static::creating(function ($penggajian) {
        if (!$penggajian->no_penggajian) {
            $last = static::latest()->first();
            $nextId = $last ? $last->id + 1 : 1;
            $penggajian->no_penggajian = 'PGJ-' . now()->format('Ymd') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }
    });
    }
}