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
        if (!Schema::hasTable('participantes')) {
            Schema::create('participantes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('representante_id')->constrained('representantes')->onDelete('cascade');
                $table->string('nombre', 50);
                $table->string('apellido', 50);
                $table->date('fecha_nacimiento')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participantes');
    }
};
