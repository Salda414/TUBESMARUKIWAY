<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder {
    public function run(): void {
        DB::table('produk')->insert([
            // Kategori Makanan Berat (kategori_id = 1)
            ['nama_produk' => 'Nasi Goreng Spesial', 'deskripsi' => 'Nasi goreng dengan telur dan ayam', 'harga' => 25000, 'stok' => 20, 'status' => 'Tersedia', 'kategori_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Ayam Geprek', 'deskripsi' => 'Ayam crispy dengan sambal pedas', 'harga' => 22000, 'stok' => 15, 'status' => 'Tersedia', 'kategori_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Mie Goreng Jawa', 'deskripsi' => 'Mie goreng khas Jawa dengan ayam dan sayuran', 'harga' => 18000, 'stok' => 25, 'status' => 'Tersedia', 'kategori_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori Makanan Ringan (kategori_id = 2)
            ['nama_produk' => 'Keripik Singkong', 'deskripsi' => 'Keripik singkong pedas manis', 'harga' => 12000, 'stok' => 30, 'status' => 'Tersedia', 'kategori_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Tahu Krispi', 'deskripsi' => 'Tahu goreng renyah dengan bumbu khas', 'harga' => 10000, 'stok' => 40, 'status' => 'Tersedia', 'kategori_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Pisang Coklat', 'deskripsi' => 'Pisang goreng dengan isian coklat', 'harga' => 15000, 'stok' => 25, 'status' => 'Tersedia', 'kategori_id' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Kategori Minuman Dingin (kategori_id = 3)
            ['nama_produk' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dengan es batu', 'harga' => 5000, 'stok' => 50, 'status' => 'Tersedia', 'kategori_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Es Kopi Susu', 'deskripsi' => 'Kopi dengan susu dan gula aren', 'harga' => 18000, 'stok' => 30, 'status' => 'Tersedia', 'kategori_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Jus Alpukat', 'deskripsi' => 'Jus alpukat dengan susu coklat', 'harga' => 20000, 'stok' => 20, 'status' => 'Tersedia', 'kategori_id' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Kategori Minuman Hangat (kategori_id = 4)
            ['nama_produk' => 'Kopi Tubruk', 'deskripsi' => 'Kopi hitam tradisional', 'harga' => 12000, 'stok' => 25, 'status' => 'Tersedia', 'kategori_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Teh Tarik', 'deskripsi' => 'Teh susu khas Malaysia', 'harga' => 15000, 'stok' => 20, 'status' => 'Tersedia', 'kategori_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama_produk' => 'Wedang Jahe', 'deskripsi' => 'Minuman jahe hangat dengan aroma rempah', 'harga' => 13000, 'stok' => 15, 'status' => 'Tersedia', 'kategori_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
