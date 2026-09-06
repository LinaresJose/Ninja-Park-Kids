<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $oldText = "NINJA PARK, C.A. VALENCIA";
        $newText = "INVERSIONES NINJA PARK VALENCIA, C.A. ";

        DB::table('terminos_condiciones')
            ->where('contenido', 'LIKE', "%{$oldText}%")
            ->update([
                'contenido' => DB::raw("REPLACE(contenido, '{$oldText}', '{$newText}')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $oldText = "NINJA PARK, C.A. VALENCIA";
        $newText = "INVERSIONES NINJA PARK VALENCIA, C.A. ";

        DB::table('terminos_condiciones')
            ->where('contenido', 'LIKE', "%{$newText}%")
            ->update([
                'contenido' => DB::raw("REPLACE(contenido, '{$newText}', '{$oldText}')")
            ]);
    }
};
