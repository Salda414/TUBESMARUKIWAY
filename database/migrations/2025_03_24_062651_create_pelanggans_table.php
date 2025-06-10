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
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); //jika parent di hapus, maka anak akan ikut terhapus
            $table->string('kode_pelanggan');
            $table->string('nama_pelanggan');
            $table->string('nomor_telepon')->unique();
            $table->string('email')->unique();
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
