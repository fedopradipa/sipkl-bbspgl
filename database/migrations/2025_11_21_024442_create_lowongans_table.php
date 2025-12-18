<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('divisi');
            $table->text('deskripsi')->nullable();

            // Validasi Periode
            $table->date('window_start_date');
            $table->date('window_end_date');
            $table->integer('min_duration'); // Durasi minimal (bulan)

            // Validasi Hari Kerja
            $table->boolean('is_fulltime')->default(true); // True = Wajib Senin-Jumat
            // 2. Simpan hari kerjanya dalam format JSON
            // Contoh isi: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"]
            $table->json('working_days')->nullable();
            // Validasi Jam
            $table->time('jam_masuk_standar')->default('08:00:00');
            $table->time('jam_pulang_standar')->default('16:00:00');

            // 2. Jurusan Terkait (String)
            $table->string('jurusan_terkait')->nullable();

            // 3. Fasilitas (Text)
            $table->text('fasilitas')->nullable();

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
