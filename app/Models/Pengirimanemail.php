<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengirimanemail extends Model
{
    use HasFactory;

    protected $fillable = [
        'penggajians_id',
        'status',
        'tgl_pengiriman_pesan',
    ];

    public function penggajian()
    {
        return $this->belongsTo(Penggajian::class, 'penggajians_id');
    }
}
