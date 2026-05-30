<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Representante;
use App\Models\TerminoCondicion;
use App\Services\RegistroService;

class RegistroAcuerdoTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_acuerdo_guarda_firma_en_storage_y_vincula_datos()
    {
        Storage::fake('public');

        // Configurar datos iniciales
        TerminoCondicion::create([
            'version' => '1.0',
            'contenido' => 'Términos de prueba',
            'activo' => true
        ]);

        $representante = Representante::create([
            'cedula' => 'V-12345678',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'fecha_nacimiento' => '1990-01-01',
        ]);

        $nuevosNiños = [
            [
                'nombre' => 'Pedrito',
                'apellido' => 'Pérez',
                'fecha_nacimiento' => '2015-05-10'
            ]
        ];

        // Simulamos un string base64 válido de una imagen pequeña (1x1 transparente)
        $firmaBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $service = new RegistroService();

        $acuerdo = $service->crearAcuerdo($representante, [], $nuevosNiños, $firmaBase64);

        // Aserciones
        $this->assertNotNull($acuerdo);
        $this->assertEquals($representante->id, $acuerdo->representante_id);
        
        // Verificar que el niño fue creado y vinculado
        $this->assertDatabaseHas('participantes', [
            'nombre' => 'Pedrito',
            'representante_id' => $representante->id
        ]);
        
        $this->assertCount(1, $acuerdo->participantes);
        $this->assertEquals('Pedrito', $acuerdo->participantes->first()->nombre);

        // Verificar que la firma se guardó en el storage público
        $this->assertStringStartsWith('firmas/firma_', $acuerdo->firma_base64);
        Storage::disk('public')->assertExists($acuerdo->firma_base64);
    }
}
