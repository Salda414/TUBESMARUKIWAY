<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori')->insert([
            ['nama' => 'Makanan', 'deskripsi' => 'Kategori untuk makanan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Minuman', 'deskripsi' => 'Kategori untuk minuman', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Cemilan', 'deskripsi' => 'Kategori untuk cemilan ringan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}