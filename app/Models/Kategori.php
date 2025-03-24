<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori'; // Pastikan ini sesuai dengan nama tabel di database

    protected $fillable = [
        'jenis_kategori',   // ✅ Tambahkan jenis di sini
        'deskripsi',
        'status',
    ];
}