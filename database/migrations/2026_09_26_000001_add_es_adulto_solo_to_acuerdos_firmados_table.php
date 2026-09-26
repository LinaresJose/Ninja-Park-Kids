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
        if (Schema::hasTable('acuerdos_firmados') && !Schema::hasColumn('acuerdos_firmados', 'es_adulto_solo')) {
            Schema::table('acuerdos_firmados', function (Blueprint $table) {
                $table->boolean('es_adulto_solo')->default(false)->after('firma_base64');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('acuerdos_firmados') && Schema::hasColumn('acuerdos_firmados', 'es_adulto_solo')) {
            Schema::table('acuerdos_firmados', function (Blueprint $table) {
                $table->dropColumn('es_adulto_solo');
            });
        }
    }
};
