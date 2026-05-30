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
        // 1. Tabla: roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_rol');
            // Sin timestamps porque Rol.php tiene public $timestamps = false;
        });

        // 2. Tabla: usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('cedula')->unique()->nullable();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->string('password');
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->boolean('estado')->default(true);
            // Sin timestamps porque User.php dice public $timestamps = false;
        });

        // 3. Tabla: representantes
        Schema::create('representantes', function (Blueprint $table) {
            $table->id();
            $table->string('cedula')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('parentesco')->nullable();
            // Sin timestamps porque Representante.php dice public $timestamps = false;
        });

        // La mayoría de edad se valida en el controlador.

        // 4. Tabla: participantes
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representante_id')->constrained('representantes')->onDelete('cascade');
            $table->string('nombre');
            $table->string('apellido');
            $table->date('fecha_nacimiento')->nullable();
            // Sin timestamps porque Participante.php dice public $timestamps = false;
        });

        // 5. Tabla: terminos_condiciones
        Schema::create('terminos_condiciones', function (Blueprint $table) {
            $table->id();
            $table->string('version')->nullable();
            $table->longText('contenido')->nullable();
            $table->boolean('activo')->default(true);
            // Sin timestamps porque TerminoCondicion.php dice public $timestamps = false;
        });

        // 6. Tabla: acuerdos_firmados
        Schema::create('acuerdos_firmados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representante_id')->constrained('representantes')->onDelete('cascade');
            $table->foreignId('terminos_id')->constrained('terminos_condiciones')->onDelete('cascade');
            $table->dateTime('fecha_firma')->nullable();
            $table->uuid('token_qr')->unique()->nullable();
            $table->longText('firma_base64')->nullable();
            // Sin timestamps porque AcuerdoFirmado.php dice public $timestamps = false;
        });

        // 7. Tabla: detalle_acuerdo_participantes
        Schema::create('detalle_acuerdo_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acuerdo_id')->constrained('acuerdos_firmados')->onDelete('cascade');
            $table->foreignId('participante_id')->constrained('participantes')->onDelete('cascade');
        });
        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('detalle_acuerdo_participantes');
        Schema::dropIfExists('acuerdos_firmados');
        Schema::dropIfExists('terminos_condiciones');
        Schema::dropIfExists('participantes');
        Schema::dropIfExists('representantes');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('roles');
    }
};
