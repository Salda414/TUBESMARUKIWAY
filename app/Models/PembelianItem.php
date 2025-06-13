<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianItem extends Model
{
    use HasFactory;

    protected $table = 'pembelian_bahan_baku_items'; 

    protected static function booted(): void
{
    static::saved(function ($item) {
        $item->pembelian->save(); // Trigger re-calculate di model parent
    });

    static::deleted(function ($item) {
        $item->pembelian->save(); // Update total kalau ada item dihapus
    });
}


    protected $fillable = [
        'pembelian_id',
        'nama_bahan',
        'harga',
        'jumlah',
        'satuan',
        'subtotal'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(PembelianBahanBaku::class, 'pembelian_id');
    }
}
