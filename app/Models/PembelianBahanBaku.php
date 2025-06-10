<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembelianBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'pembelian_bahan_baku';

    protected static function booted(): void
{
    static::saving(function ($pembelian) {
        $pembelian->total_harga = $pembelian->items->sum(function ($item) {
            return $item->harga * $item->jumlah;
        });
    });
}


    protected $fillable = [
        'vendor_id',
        'tanggal_pembelian',
        'status',
        'total_harga',
        'catatan'
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'total_harga' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PembelianItem::class, 'pembelian_id');
    }
}
