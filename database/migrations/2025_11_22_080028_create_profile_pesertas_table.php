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
        Schema::create('profile_pesertas', function (Blueprint $table) {
            $table->id();
            // Cascade: Jika user dihapus, profil ikut terhapus
            $table->foreignId('pengguna_id')->constrained('users')->onDelete('cascade');
            $table->string('nim_nis', 50);
            $table->string('institusi'); // Nama Sekolah/Kampus
            $table->string('jurusan');
            $table->string('no_hp', 20);
            $table->text('alamat')->nullable();
            $table->string('foto_profil', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_pesertas');
    }
};
