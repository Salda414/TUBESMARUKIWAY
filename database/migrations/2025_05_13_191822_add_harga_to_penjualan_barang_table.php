<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            // Tambahkan kolom harga setelah kolom jumlah (atau sesuaikan posisinya)
            $table->decimal('harga', 15, 2)->after('jumlah'); // Contoh: 15 digit total, 2 digit desimal
            // Atau jika harga selalu bilangan bulat:
            // $table->integer('harga')->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
};