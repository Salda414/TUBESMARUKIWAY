<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            $table->decimal('harga_beli', 12, 2)->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_barang', function (Blueprint $table) {
            $table->decimal('harga_beli', 12, 2)->nullable(false)->default(0)->change();
        });
    }
};
