<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstadisticasService
{
    /**
     * Retorna todos los datos de estadísticas para el dashboard.
     */
    public function getDashboardData(): array
    {
        return [
            'afluencia' => $this->getAfluencia(),
            'horas'     => $this->getHorasPico(),
            'demo'      => $this->getDemografia(),
        ];
    }

    /**
     * Afluencia de los últimos 7 días vs semana anterior.
     */
    public function getAfluencia(): array
    {
        $afluenciaActual = DB::table('acuerdos_firmados')
            ->whereNotNull('fecha_firma')
            ->where('fecha_firma', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(fecha_firma) as fecha, COUNT(id) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        $afluenciaAnterior = DB::table('acuerdos_firmados')
            ->whereNotNull('fecha_firma')
            ->whereBetween('fecha_firma', [
                Carbon::now()->subDays(13)->startOfDay(),
                Carbon::now()->subDays(7)->endOfDay(),
            ])
            ->selectRaw('DATE(fecha_firma) as fecha, COUNT(id) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        // Rellenar días sin actividad con 0
        $dias    = $this->rellenarDias(6, $afluenciaActual);
        $diasAnt = $this->rellenarDiasAnteriores(6, $afluenciaAnterior);

        Carbon::setLocale('es');
        $labels = collect($dias->keys())->map(
            fn($d) => ucfirst(Carbon::parse($d)->translatedFormat('D d'))
        )->values();

        return [
            'labels'   => $labels,
            'data'     => $dias->values(),
            'data_ant' => $diasAnt->values(),
        ];
    }

    /**
     * Horas pico históricas de entradas al parque.
     */
    public function getHorasPico(): array
    {
        $horas = DB::table('acuerdos_firmados')
            ->whereNotNull('fecha_firma')
            ->selectRaw('HOUR(fecha_firma) as hora, COUNT(id) as total')
            ->groupBy('hora')
            ->orderBy('hora', 'asc')
            ->get();

        $labels = $horas->map(fn($h) => str_pad($h->hora, 2, '0', STR_PAD_LEFT) . ':00')->toArray();
        $data   = $horas->pluck('total')->toArray();

        return compact('labels', 'data');
    }

    /**
     * Distribución demográfica por grupos de edad de los participantes.
     */
    public function getDemografia(): array
    {
        $edades = DB::table('participantes')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 0 AND 3 THEN 1 ELSE 0 END), 0) as g1,
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 4 AND 7 THEN 1 ELSE 0 END), 0) as g2,
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 8 AND 12 THEN 1 ELSE 0 END), 0) as g3,
                COALESCE(SUM(CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) > 12 THEN 1 ELSE 0 END), 0) as g4
            ')
            ->first();

        $data  = [$edades->g1, $edades->g2, $edades->g3, $edades->g4];
        $total = array_sum($data);

        $labels = ['1-3 años', '4-7 años', '8-12 años', '+13 años'];

        if ($total > 0) {
            $labels = [
                '1-3 años ('  . round(($edades->g1 / $total) * 100) . '%)',
                '4-7 años ('  . round(($edades->g2 / $total) * 100) . '%)',
                '8-12 años (' . round(($edades->g3 / $total) * 100) . '%)',
                '+13 años ('  . round(($edades->g4 / $total) * 100) . '%)',
            ];
        }

        return compact('labels', 'data');
    }

    // --- Helpers privados ---

    private function rellenarDias(int $rango, $collection): \Illuminate\Support\Collection
    {
        $dias = collect();
        for ($i = $rango; $i >= 0; $i--) {
            $dias[Carbon::now()->subDays($i)->format('Y-m-d')] = 0;
        }
        foreach ($collection as $item) {
            $dias[$item->fecha] = $item->total;
        }
        return $dias;
    }

    private function rellenarDiasAnteriores(int $rango, $collection): \Illuminate\Support\Collection
    {
        $dias = collect();
        for ($i = $rango; $i >= 0; $i--) {
            $dias[Carbon::now()->subDays($i + 7)->format('Y-m-d')] = 0;
        }
        foreach ($collection as $item) {
            $dias[$item->fecha] = $item->total;
        }
        return $dias;
    }
}
