<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RegistrosExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected string $desde;
    protected string $hasta;

    public function __construct(string $desde, string $hasta)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
    }

    /**
     * JOIN complejo:
     * acuerdos_firmados → representantes → detalle_acuerdo_participantes → participantes
     */
    public function query()
    {
        return DB::table('acuerdos_firmados as af')
            ->join('representantes as r', 'af.representante_id', '=', 'r.id')
            ->join('detalle_acuerdo_participantes as dap', 'af.id', '=', 'dap.acuerdo_id')
            ->join('participantes as p', 'dap.participante_id', '=', 'p.id')
            ->whereBetween('af.fecha_firma', [
                Carbon::parse($this->desde)->startOfDay(),
                Carbon::parse($this->hasta)->endOfDay(),
            ])
            ->whereNotNull('af.fecha_firma')
            ->select(
                'af.id as acuerdo_id',
                'r.nombre as rep_nombre',
                'r.apellido as rep_apellido',
                'r.correo',
                'r.telefono',
                'r.cedula',
                'r.fecha_nacimiento as rep_fnac',
                'r.parentesco',
                'p.nombre as part_nombre',
                'p.apellido as part_apellido',
                'p.fecha_nacimiento as part_fnac',
                'af.fecha_firma'
            )
            ->orderBy('af.fecha_firma', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID Acuerdo',
            'Nombre Representante',
            'Correo',
            'Teléfono',
            'Cédula',
            'F. Nac. Representante',
            'Parentesco',
            'Nombre del Menor',
            'F. Nac. Menor',
            'Edad del Menor',
            'Fecha de Firma',
            'Hora de Firma',
        ];
    }

    public function map($row): array
    {
        $edadMenor = null;
        if ($row->part_fnac) {
            $edadMenor = Carbon::parse($row->part_fnac)->age . ' años';
        }

        $fechaFirma = $row->fecha_firma ? Carbon::parse($row->fecha_firma) : null;

        return [
            $row->acuerdo_id,
            trim($row->rep_nombre . ' ' . $row->rep_apellido),
            $row->correo ?? '—',
            $row->telefono ?? '—',
            $row->cedula,
            $row->rep_fnac ? Carbon::parse($row->rep_fnac)->format('d/m/Y') : '—',
            $row->parentesco ?? '—',
            trim($row->part_nombre . ' ' . $row->part_apellido),
            $row->part_fnac ? Carbon::parse($row->part_fnac)->format('d/m/Y') : '—',
            $edadMenor ?? '—',
            $fechaFirma ? $fechaFirma->format('d/m/Y') : '—',
            $fechaFirma ? $fechaFirma->format('H:i:s') : '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Fila de encabezados (fila 1) en negrita con fondo morado
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF6D28D9'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Registros Ninja Park';
    }
}
