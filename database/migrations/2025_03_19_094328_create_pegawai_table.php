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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('id_pegawai',4);
            $table->string('nama',50);
            $table->enum('jenis_kelamin',['Perempuan','Laki-laki']);
            $table->string('alamat',100);
            $table->string('email',50);
            $table->string('no_telpon',15);
            $table->string('posisi',50);
            $table->integer('gaji');
            $table->timestamps()
            ;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
