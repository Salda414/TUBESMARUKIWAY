<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualan_barang', 'produk_id')) {
                // Sesuaikan tipe data dan constraint foreign key dengan tabel produk Anda
                $table->foreignId('produk_id')->constrained('produk')->after('penjualan_id'); 
            }
            if (!Schema::hasColumn('penjualan_barang', 'jumlah')) {
                $table->integer('jumlah')->default(1)->after('produk_id');
            }
            if (!Schema::hasColumn('penjualan_barang', 'harga')) {
                $table->decimal('harga', 15, 2)->default(0)->after('jumlah');
            }
            // Tambahkan kolom lain jika perlu, misal 'tgl' per item jika ada
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            // Hati-hati dengan drop column jika data sudah ada
            // if (Schema::hasColumn('penjualan_barang', 'produk_id')) {
            //     $table->dropForeign(['produk_id']);
            //     $table->dropColumn('produk_id');
            // }
            // if (Schema::hasColumn('penjualan_barang', 'jumlah')) {
            //     $table->dropColumn('jumlah');
            // }
            // if (Schema::hasColumn('penjualan_barang', 'harga')) {
            //     $table->dropColumn('harga');
            // }
        });
    }
};