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
            if (!Schema::hasColumn('penjualan_barang', 'jumlah')) {
                $table->integer('jumlah')->after('produk_id');
            }
    
            if (!Schema::hasColumn('penjualan_barang', 'harga')) {
                $table->decimal('harga', 15, 2)->after('jumlah');
            }
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            $table->dropColumn(['jumlah', 'harga']);
        });
    }
};
