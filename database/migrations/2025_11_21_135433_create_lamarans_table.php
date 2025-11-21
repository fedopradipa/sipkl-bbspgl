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
        Schema::create('lamarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lowongan_id')->constrained('lowongans')->onDelete('cascade');

            // Nullable: Karena baru diisi Admin saat status = diterima
            $table->foreignId('pembimbing_id')->nullable()->constrained('pembimbings')->onDelete('set null');

            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('pending');
            $table->text('catatan_admin')->nullable(); // Alasan tolak

            // Data Usulan Peserta
            $table->date('tgl_mulai_usulan');
            $table->date('tgl_selesai_usulan');
            $table->json('hari_kerja_usulan'); // Simpan ["Monday", "Tuesday"]
            $table->text('alasan_hari_kerja')->nullable();

            // Data Resmi (Keputusan Admin)
            $table->date('tgl_mulai_resmi')->nullable();
            $table->date('tgl_selesai_resmi')->nullable();
            $table->json('hari_kerja_resmi')->nullable(); // Acuan untuk Absensi

            // File
            $table->string('path_surat_balasan')->nullable();
            $table->string('path_laporan_akhir')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lamarans');
    }
};
