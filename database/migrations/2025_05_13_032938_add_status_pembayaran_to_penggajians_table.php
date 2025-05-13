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
        Schema::table('penggajians', function (Blueprint $table) {
            $table->enum('status_pembayaran', ['dibayar', 'belum_dibayar'])->default('belum_dibayar');
        });
    }

    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            $table->dropColumn('status_pembayaran');
        });
    }

};
