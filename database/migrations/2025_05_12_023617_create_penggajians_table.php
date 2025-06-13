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
        Schema::create('penggajians', function (Blueprint $table) {
            $table->id();
            $table->string('pegawai_id', 4);    
            $table->foreign('pegawai_id')
                ->references('id_pegawai')
                ->on('pegawais')
                ->onDelete('cascade');            
            
            $table->integer('jumlah_hadir')->default(0);    
            $table->integer('gaji_per_hari')->default(0);
            $table->integer('total_gaji')->default(0);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajians');
    }
};
