<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pembelian_bahan_baku', function (Blueprint $table) {
            $table->id();

            // Ganti produk_id dengan nama_produk manual
            $table->string('nama_produk'); // User bisa ketik langsung

            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');

            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->date('tanggal_pembelian');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pembelian_bahan_baku');
    }
};
