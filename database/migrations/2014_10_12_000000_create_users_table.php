<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');                 // default Laravel
            $table->string('email')->unique();      // default Laravel

            // role_id (FK -> roles.id_role)
            $table->foreignId('role_id')
                ->nullable()
                ->constrained('roles', 'id_role')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->timestamp('email_verified_at')->nullable(); // default Laravel
            $table->string('password');                         // default Laravel
            $table->rememberToken();                            // default Laravel
            $table->timestamps();                               // default Laravel
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
