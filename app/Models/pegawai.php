<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';
    protected $primaryKey = 'id_pegawai';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pegawai', 'nama', 'jenis_kelamin', 'alamat', 'email', 'no_telpon', 'posisi', 'gaji'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pegawai) {
            if (empty($pegawai->id_pegawai)) {
                $pegawai->id_pegawai = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Tambahkan relasi di sini
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'pegawai_id', 'id_pegawai');
    }

    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'pegawai_id', 'id_pegawai');
    }
}
