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
                $table->string('cedula', 8)->unique();
                $table->string('nombre', 50);
                $table->string('apellido', 50);
                $table->date('fecha_nacimiento')->nullable();
                $table->string('correo', 100)->nullable();
                $table->string('telefono', 11)->nullable();
                $table->string('parentesco', 30)->nullable();
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
