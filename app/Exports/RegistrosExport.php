<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RegistrosExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithCustomStartCell, WithDrawings, WithEvents
{
    protected string $desde;
    protected string $hasta;
    protected array $columnas;

    protected array $mapColumnas = [
        'acuerdo_id' => 'ID Acuerdo',
        'rep_nombre' => 'Representante',
        'correo' => 'Correo',
        'telefono' => 'Teléfono',
        'cedula' => 'Cédula',
        'rep_fnac' => 'F. Nac. Representante',
        'parentesco' => 'Parentesco',
        'part_nombre' => 'Menor',
        'part_fnac' => 'F. Nac. Menor',
        'edad_menor' => 'Edad Menor',
        'fecha_firma' => 'Fecha Firma',
        'hora_firma' => 'Hora Firma',
    ];

    public function __construct(string $desde, string $hasta, array $columnas = [])
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->columnas = empty($columnas) ? array_keys($this->mapColumnas) : $columnas;
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

    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        $headers = [];
        foreach ($this->columnas as $key) {
            if (isset($this->mapColumnas[$key])) {
                $headers[] = $this->mapColumnas[$key];
            }
        }
        return $headers;
    }

    public function map($row): array
    {
        $mapped = [];
        
        $edadMenor = null;
        if ($row->part_fnac) {
            $edadMenor = Carbon::parse($row->part_fnac)->age . ' años';
        }

        $fechaFirma = $row->fecha_firma ? Carbon::parse($row->fecha_firma) : null;

        foreach ($this->columnas as $key) {
            switch ($key) {
                case 'acuerdo_id':
                    $mapped[] = $row->acuerdo_id;
                    break;
                case 'rep_nombre':
                    $mapped[] = trim($row->rep_nombre . ' ' . $row->rep_apellido);
                    break;
                case 'correo':
                    $mapped[] = $row->correo ?? '—';
                    break;
                case 'telefono':
                    $mapped[] = $row->telefono ?? '—';
                    break;
                case 'cedula':
                    $mapped[] = $row->cedula;
                    break;
                case 'rep_fnac':
                    $mapped[] = $row->rep_fnac ? Carbon::parse($row->rep_fnac)->format('d/m/Y') : '—';
                    break;
                case 'parentesco':
                    $mapped[] = $row->parentesco ?? '—';
                    break;
                case 'part_nombre':
                    $mapped[] = trim($row->part_nombre . ' ' . $row->part_apellido);
                    break;
                case 'part_fnac':
                    $mapped[] = $row->part_fnac ? Carbon::parse($row->part_fnac)->format('d/m/Y') : '—';
                    break;
                case 'edad_menor':
                    $mapped[] = $edadMenor ?? '—';
                    break;
                case 'fecha_firma':
                    $mapped[] = $fechaFirma ? $fechaFirma->format('d/m/Y') : '—';
                    break;
                case 'hora_firma':
                    $mapped[] = $fechaFirma ? $fechaFirma->format('H:i:s') : '—';
                    break;
            }
        }
        
        return $mapped;
    }

    public function drawings()
    {
        $logoPath = public_path('img/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo Corporativo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(10);
            return $drawing;
        }
        return [];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            6 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4C1D95'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->getRowDimension('1')->setRowHeight(25);
                $sheet->getRowDimension('2')->setRowHeight(20);
                $sheet->getRowDimension('3')->setRowHeight(20);
                $sheet->getRowDimension('4')->setRowHeight(20);
                $sheet->getRowDimension('5')->setRowHeight(15);
                $sheet->getRowDimension('6')->setRowHeight(26);
                
                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(18);

                $sheet->setCellValue('B1', 'NINJA PARK KIDS - REPORTE DE REGISTROS');
                
                $sheet->setCellValue('B2', 'Fecha de Generación:');
                $sheet->setCellValue('C2', Carbon::now()->format('d/m/Y H:i:s'));
                
                $sheet->setCellValue('B3', 'Rango de Reporte:');
                $sheet->setCellValue('C3', Carbon::parse($this->desde)->format('d/m/Y') . ' al ' . Carbon::parse($this->hasta)->format('d/m/Y'));
                
                $sheet->setCellValue('B4', 'Generado por:');
                $sheet->setCellValue('C4', Auth::user() ? Auth::user()->nombre . ' (' . Auth::user()->rol->nombre_rol . ')' : 'Sistema');

                $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(15)->setName('Arial')->setColor(new Color('FF4C1D95'));
                $sheet->getStyle('B2:B4')->getFont()->setBold(true)->setName('Arial')->setSize(10)->setColor(new Color('FF374151'));
                $sheet->getStyle('C2:C4')->getFont()->setBold(false)->setName('Arial')->setSize(10)->setColor(new Color('FF4B5563'));

                $colCount = count($this->columnas);
                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
                
                $highestRow = $sheet->getHighestRow();
                if ($highestRow >= 6) {
                    $sheet->getStyle('A6:' . $lastColLetter . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FFE2E8F0'));
                }
            }
        ];
    }

    public function title(): string
    {
        return 'Registros Ninja Park';
    }
}
