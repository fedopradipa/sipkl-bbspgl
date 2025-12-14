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
        Schema::create('profile_admins', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel users (Wajib)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Data Identitas Resmi
            $table->string('nip', 50)->unique()->nullable(); // NIP bisa unik, tapi buat nullable sementara
            $table->string('jabatan', 100)->nullable();

            // Kontak dan Profil
            $table->string('no_hp', 20)->nullable();
            $table->string('foto_profil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_admins');
    }
};
