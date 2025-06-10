<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengirimanemails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penggajians_id')->constrained('penggajians')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->dateTime('tgl_pengiriman_pesan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengirimanemails');
    }
};
