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
        if (!Schema::hasTable('acuerdos_firmados')) {
            Schema::create('acuerdos_firmados', function (Blueprint $table) {
                $table->id();
                $table->foreignId('representante_id')->constrained('representantes')->onDelete('cascade');
                $table->foreignId('terminos_id')->constrained('terminos_condiciones')->onDelete('cascade');
                $table->dateTime('fecha_firma')->nullable();
                $table->uuid('token_qr')->unique()->nullable();
                $table->longText('firma_base64')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acuerdos_firmados');
    }
};
