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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('pengguna_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lamaran_id')->constrained('lamarans')->onDelete('cascade');

            $table->date('tanggal');

            // Waktu Absen
            $table->time('jam_masuk')->nullable();

            $table->enum('status', [
                'hadir',
                'terlambat',
                'sakit',
                'izin',
                'alpha'
            ]);

            // NOTE: Kolom Geolocation (lat/long) SUDAH DIHAPUS sesuai request.

            $table->text('catatan_harian')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
