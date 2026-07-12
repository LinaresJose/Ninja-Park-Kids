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

            // Evitar duplicar el acuerdo si ya se procesó uno hoy (por doble click o envío simultáneo)
            $acuerdoExistente = AcuerdoFirmado::where('representante_id', $representante->id)
                ->whereDate('fecha_firma', \Carbon\Carbon::today())
                ->first();

            if ($acuerdoExistente) {
                return $acuerdoExistente;
            }

            // 1. Crear niños nuevos si los hay (reusando existentes si coinciden exactamente para evitar duplicados en la BD) y recopilar sus IDs
            foreach ($nuevosNiños as $niño) {
                $existente = Participante::where('representante_id', $representante->id)
                    ->where('nombre', $niño['nombre'])
                    ->where('apellido', $niño['apellido'])
                    ->where('fecha_nacimiento', $niño['fecha_nacimiento'])
                    ->first();

                if ($existente) {
                    $participantesIds[] = $existente->id;
                } else {
                    $creado = Participante::create([
                        'representante_id' => $representante->id,
                        'nombre'           => $niño['nombre'],
                        'apellido'         => $niño['apellido'],
                        'fecha_nacimiento' => $niño['fecha_nacimiento'],
                    ]);
                    $participantesIds[] = $creado->id;
                }
            }

            // 2. Obtener la versión activa de Términos y Condiciones
            $termino = TerminoCondicion::where('activo', true)->first();

            if (!$termino) {
                throw new \RuntimeException(
                    'No hay versión de Términos y Condiciones activa. '
                    . 'Un administrador debe activar una versión desde el panel legal antes de continuar.'
                );
            }

            // 3. Decodificar y guardar la firma digital en disco (si es un base64 nuevo)
            $firmaGuardada = $firmaBase64;
            if (str_starts_with($firmaBase64, 'data:image/')) {
                // Extraer el tipo MIME y la data Base64 de forma genérica (soporta png, jpeg, webp, etc.)
                if (!preg_match('/^data:image\/(\w+);base64,(.+)$/s', $firmaBase64, $parts)) {
                    throw new \InvalidArgumentException('La firma digital tiene un formato Base64 inválido.');
                }

                $imageData = base64_decode($parts[2], true); // strict: rechaza Base64 mal formado

                if ($imageData === false) {
                    throw new \InvalidArgumentException('No se pudo decodificar la firma digital. El Base64 está corrupto.');
                }

                // Normalizar extensión: webp/jpeg -> png para consistencia en disco
                $extension     = in_array($parts[1], ['jpeg', 'jpg', 'webp']) ? 'png' : $parts[1];
                $firmaFileName = 'firmas/firma_' . Str::uuid() . '.' . $extension;
                Storage::disk('public')->put($firmaFileName, $imageData);
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

            // 5. Vincular los participantes a este acuerdo (eliminando duplicados del array de IDs)
            $acuerdo->participantes()->sync(array_unique($participantesIds));

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
