<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran'; // Nama tabel eksplisit

    protected $guarded = [];
    
    public function penjualan()
{
    return $this->belongsTo(penjualan::class);
}
protected $casts = [
    'tgl_bayar'        => 'datetime',
    'transaction_time' => 'datetime',
    'gross_amount'     => 'float', 
];

}