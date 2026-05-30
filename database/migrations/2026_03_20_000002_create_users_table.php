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
        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->id();
                $table->string('cedula')->unique()->nullable();
                $table->string('nombre');
                $table->string('apellido');
                $table->string('correo')->unique();
                $table->string('password');
                $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
                $table->boolean('estado')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
