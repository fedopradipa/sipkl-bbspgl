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
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->string('posisi');
            $table->text('deskripsi')->nullable();

            // Validasi Periode Magang
            $table->date('window_start_date');
            $table->date('window_end_date');
            $table->integer('min_duration'); // Durasi minimal (bulan)

            // Validasi Hari Kerja
            $table->boolean('is_fulltime')->default(true);
            $table->json('hari_kerja')->nullable(); // ["Senin", "Selasa", ...]

            // === KONFIGURASI JAM KERJA (BEST PRACTICE) ===

            // 1. Jam tombol absen mulai muncul/bisa diklik (misal 07:00)
            $table->time('jam_buka_absen')->default('07:00:00');

            // 2. Jam masuk resmi (misal 08:00)
            $table->time('jam_masuk_standar')->default('08:00:00');


            // 4. Jam batas akhir absen masuk (misal 09:00). Lewat ini dianggap Alpha.
            $table->time('jam_tutup_absen')->default('09:00:00');


            // Metadata Tambahan
            $table->string('jurusan_terkait')->nullable();

            $table->boolean('is_open')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lowongans');
    }
};
