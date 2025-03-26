<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateCoaTable extends Migration
{
    /**
     * Run the migrations.
     */    
    public function up(): void
    {
        
        Schema::create('coa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_akun')->unique();
            $table->string('nama_akun')->unique();
            $table->string('header_akun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa');
    }
};