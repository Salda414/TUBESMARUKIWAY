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
            Schema::create('absensis', function (Blueprint $table) {
                $table->id();
                $table->string('pegawai_id', 4);
                $table->foreign('pegawai_id')->references('id_pegawai')->on('pegawais')->onDelete('cascade');                
                $table->date('tanggal');
                $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa']);
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('absensis');
        }
    };
