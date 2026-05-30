<?php

namespace App\Services;

use App\Models\Participante;
use App\Models\AcuerdoFirmado;
use App\Models\TerminoCondicion;
use App\Models\Representante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RegistroService
{
    /**
     * Crea un acuerdo firmado completo dentro de una transacción atómica.
     *
     * Este método centraliza la lógica de negocio que estaba duplicada
     * en RegistroController::store() y RegistroController::guardarFirma().
     *
     * @param  Representante  $representante  El representante que firma.
     * @param  array          $participantesIds  IDs de participantes existentes seleccionados.
     * @param  array          $nuevosNiños  Array con datos de niños nuevos a crear.
     * @param  string         $firmaBase64  La firma digital en base64.
     * @return AcuerdoFirmado El acuerdo creado.
     */
    public function crearAcuerdo(
        Representante $representante,
        array $participantesIds,
        array $nuevosNiños,
        string $firmaBase64
    ): AcuerdoFirmado {
        return DB::transaction(function () use ($representante, $participantesIds, $nuevosNiños, $firmaBase64) {

            // 1. Crear niños nuevos si los hay y recopilar sus IDs
            foreach ($nuevosNiños as $niño) {
                $creado = Participante::create([
                    'representante_id' => $representante->id,
                    'nombre'           => $niño['nombre'],
                    'apellido'         => $niño['apellido'],
                    'fecha_nacimiento' => $niño['fecha_nacimiento'],
                ]);
                $participantesIds[] = $creado->id;
            }

            // 2. Obtener la versión activa de Términos y Condiciones
            $termino = TerminoCondicion::where('activo', true)->firstOrFail();

            // 3. Decodificar y guardar la firma digital en disco (si es un base64 nuevo)
            $firmaGuardada = $firmaBase64;
            if (str_starts_with($firmaBase64, 'data:image/')) {
                $image = str_replace([
                    'data:image/png;base64,',
                    'data:image/jpeg;base64,',
                    'data:image/jpg;base64,',
                ], '', $firmaBase64);
                $image = str_replace(' ', '+', $image);
                $firmaFileName = 'firmas/firma_' . uniqid() . '_' . time() . '.png';
                Storage::disk('public')->put($firmaFileName, base64_decode($image));
                $firmaGuardada = $firmaFileName;
            }

            // 4. Crear el Acuerdo Firmado (pase del día)
            $acuerdo = AcuerdoFirmado::create([
                'representante_id' => $representante->id,
                'terminos_id'      => $termino->id,
                'fecha_firma'      => now(),
                'token_qr'         => (string) Str::uuid(),
                'firma_base64'     => $firmaGuardada,
            ]);

            // 5. Vincular los participantes a este acuerdo
            $acuerdo->participantes()->sync($participantesIds);

            return $acuerdo;
        });
    }

    /**
     * Filtra y devuelve los IDs de participantes existentes que realmente
     * pertenecen al representante dado, previniendo manipulación de datos.
     *
     * @param  Representante  $representante
     * @param  array          $participanteIdsEnviados
     * @return array
     */
    public function filtrarParticipantesValidos(Representante $representante, array $participanteIdsEnviados): array
    {
        // Validamos en una sola query que los IDs pertenezcan al representante
        return Participante::where('representante_id', $representante->id)
            ->whereIn('id', $participanteIdsEnviados)
            ->pluck('id')
            ->toArray();
    }
}
