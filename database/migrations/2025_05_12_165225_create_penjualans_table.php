<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            
            // Menambahkan foreign key pelanggan_id
            $table->foreignId('pelanggan_id')
                  ->constrained('pelanggan', 'id_pelanggan') // Pastikan id_pelanggan ada di tabel pelanggan
                  ->onDelete('cascade');

            // Menambahkan kolom no_faktur, status, tgl, dll
            $table->string('no_faktur');
            $table->string('status');
            $table->datetime('tgl');
            $table->decimal('tagihan', 15, 2)->nullable();

            // Menambahkan foreign key produk_id
            $table->foreignId('produk_id')
                  ->constrained('produk', 'id_produk') // Pastikan id_produk ada di tabel produk
                  ->onDelete('cascade');

            // Menambahkan foreign key kategori_id
            $table->foreignId('kategori_id')
                  ->constrained('kategori') // Relasi ke id di tabel kategori
                  ->onDelete('cascade');  // Bisa tambahkan onDelete('cascade') jika ingin

            // Menambahkan kolom jumlah_item, harga_per_item, dan total_harga
            $table->integer('jumlah_item')->default(1);
            $table->decimal('harga_per_item', 15, 2);
            $table->decimal('total_harga', 15, 2);

            // Kolom timestamps otomatis untuk created_at dan updated_at
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Menghapus tabel penjualan jika rollback
        Schema::dropIfExists('penjualan');
    }
};
