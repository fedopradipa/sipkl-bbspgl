<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_pengajuan_izins_table.php

    public function up()
    {
        Schema::create('pengajuan_izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lamaran_id')->constrained('lamarans')->onDelete('cascade');

            $table->enum('jenis_izin', ['sakit', 'izin']);

            // GANTI INI: Dari range tanggal menjadi JSON list tanggal
            // Isinya nanti: ["2025-12-01", "2025-12-05"]
            $table->json('tanggal_izin');

            $table->text('alasan');
            $table->string('bukti_foto')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izins');
    }
};
