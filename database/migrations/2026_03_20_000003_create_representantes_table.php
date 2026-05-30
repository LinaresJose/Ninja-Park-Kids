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
        if (!Schema::hasTable('representantes')) {
            Schema::create('representantes', function (Blueprint $table) {
                $table->id();
                $table->string('cedula')->unique();
                $table->string('nombre');
                $table->string('apellido');
                $table->date('fecha_nacimiento')->nullable();
                $table->string('correo')->nullable();
                $table->string('telefono')->nullable();
                $table->string('parentesco')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representantes');
    }
};
