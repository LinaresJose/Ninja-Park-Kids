<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TerminoCondicion;

$termino = TerminoCondicion::where('activo', true)->first();

if (!$termino) {
    echo "ERROR: No se encontró ningún término activo.\n";
    exit(1);
}

$contenido = $termino->contenido;

// Buscar el inicio del bloque a eliminar
$marca = '• He leído, entiendo y acepto';
$pos = strpos($contenido, $marca);

if ($pos === false) {
    echo "AVISO: El texto a eliminar no fue encontrado. Puede que ya haya sido eliminado.\n";
    exit(0);
}

$nuevo = rtrim(substr($contenido, 0, $pos));
$termino->contenido = $nuevo;
$termino->save();

echo "OK: Texto eliminado correctamente.\n";
echo "Longitud anterior: " . strlen($contenido) . " chars\n";
echo "Longitud nueva:    " . strlen($nuevo) . " chars\n";
