<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->bigIncrements('id');  // id sebagai primary key
            $table->string('jenis_kategori')->unique(); // Nama kategori harus unik
            $table->text('deskripsi')->nullable(); // Deskripsi kategori (opsional)
            $table->enum('status', ['Tersedia', 'Tidak Tersedia'])->default('Tersedia'); // Status kategori
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};