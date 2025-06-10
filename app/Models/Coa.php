<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    /** @use HasFactory<\Database\Factories\CoaFactory> */
    use HasFactory;

    protected $fillable = [
        'header_akun',
        'kode_akun',
        'nama_akun',
    ];
    protected $table = 'coa';

    protected $guarded = [];

    public function journalDetail() //untuk relasi ke model Jurnal_detail
    {
        return $this->hasMany(JurnalDetail::class);
    }
}
