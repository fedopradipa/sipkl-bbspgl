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
            // WAJIB: Relasi Langsung ke USER
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lamaran_id')->constrained('lamarans')->onDelete('cascade');
            $table->date('tanggal');

            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            $table->enum('status', [
                'hadir',
                'terlambat',
                'sakit',
                'izin',
                'alpha'
            ]);
            // Geolocation (Pakai Decimal untuk akurasi GPS)
            $table->decimal('lat_masuk', 10, 8)->nullable();
            $table->decimal('long_masuk', 11, 8)->nullable();
            $table->decimal('lat_pulang', 10, 8)->nullable();
            $table->decimal('long_pulang', 11, 8)->nullable();

            $table->text('catatan_harian')->nullable();
            $table->boolean('is_auto_closed')->default(false); // Logic "Lazy Update"

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
