<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('produk', function (Blueprint $table) {
            $table->bigIncrements('id_produk');  // id_produk sebagai primary key
            $table->string('nama_produk'); 
            $table->text('deskripsi')->nullable(); 
            $table->string('gambar'); // Path gambar
            $table->decimal('harga', 10, 2); 
            $table->integer('stok'); 
            $table->enum('status', ['Tersedia', 'Tidak Tersedia'])->default('Tersedia'); 
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('produk');
    }
};
