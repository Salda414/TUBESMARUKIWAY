<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'satuan',
        'stok',
        'harga_bahan',
    ];

    // Relasi ke pembelian bahan (jika ada)
    public function pembelianBahan()
    {
        return $this->hasMany(PembelianBahan::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->kode_bahan) {
                $lastKode = static::orderByDesc('id')->value('kode_bahan');

                if ($lastKode) {
                    $number = (int) str_replace('KB-', '', $lastKode);
                    $nextNumber = $number + 1;
                } else {
                    $nextNumber = 1;
                }

                $model->kode_bahan = 'KB-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
