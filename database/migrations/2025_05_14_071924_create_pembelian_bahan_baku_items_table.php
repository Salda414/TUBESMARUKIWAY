<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembelian_bahan_baku_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian_bahan_baku')->onDelete('cascade');
            $table->string('nama_bahan');
            $table->decimal('harga', 15, 2);
            $table->decimal('jumlah', 10, 2);
            $table->string('satuan');
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembelian_bahan_baku_items');
    }
};