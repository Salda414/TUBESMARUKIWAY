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
            // Hanya hapus kolom jika memang ada
            if (Schema::hasColumn('penjualan_barang', 'tgl')) {
                $table->dropColumn('tgl');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            // Tambahkan kembali kolom jika migrasi di-rollback (sesuaikan tipe data jika perlu)
            $table->date('tgl')->nullable(); // Atau timestamp, datetime, dll.
        });
    }
};
