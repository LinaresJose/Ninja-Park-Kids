<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RegistrosExport;
use App\Http\Controllers\Controller;
use App\Models\AcuerdoFirmado;
use App\Models\Representante;
use App\Models\TerminoCondicion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ReportesController extends Controller
{
    /**
     * Vista principal del módulo Reportes y Descargas.
     */
    public function index()
    {
        if (!Auth::user()->tieneAccesoAdmin()) {
            abort(403, 'Acceso no autorizado.');
        }

        return view('admin.reportes');
    }

    /**
     * Descarga el Excel con el JOIN complejo filtrado por rango de fechas.
     */
    public function exportarExcel(Request $request)
    {
        if (!Auth::user()->tieneAccesoAdmin()) {
            abort(403, 'Acceso no autorizado.');
        }

        $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
            'columnas' => 'nullable|array',
        ]);

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $columnas = $request->input('columnas', []);

        $filename = 'ninja-park-registros_' . $desde . '_al_' . $hasta . '.xlsx';

        return Excel::download(new RegistrosExport($desde, $hasta, $columnas), $filename);
    }

    /**
     * Descarga el PDF con el listado masivo en diseño horizontal Landscape.
     */
    public function exportarPdf(Request $request)
    {
        if (!Auth::user()->tieneAccesoAdmin()) {
            abort(403, 'Acceso no autorizado.');
        }

        $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
            'columnas' => 'nullable|array',
        ]);

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $columnas = $request->input('columnas', [
            'acuerdo_id', 'rep_nombre', 'correo', 'telefono', 'cedula', 
            'rep_fnac', 'parentesco', 'part_nombre', 'part_fnac', 
            'edad_menor', 'fecha_firma', 'hora_firma'
        ]);

        // Mapeo de cabeceras para la vista
        $mapColumnas = [
            'acuerdo_id' => 'ID Acuerdo',
            'rep_nombre' => 'Representante',
            'correo' => 'Correo',
            'telefono' => 'Teléfono',
            'cedula' => 'Cédula',
            'rep_fnac' => 'F. Nac. Rep.',
            'parentesco' => 'Parentesco',
            'part_nombre' => 'Menor',
            'part_fnac' => 'F. Nac. Menor',
            'edad_menor' => 'Edad Menor',
            'fecha_firma' => 'Fecha Firma',
            'hora_firma' => 'Hora Firma',
        ];

        // Consulta de registros
        $registros = DB::table('acuerdos_firmados as af')
            ->join('representantes as r', 'af.representante_id', '=', 'r.id')
            ->join('detalle_acuerdo_participantes as dap', 'af.id', '=', 'dap.acuerdo_id')
            ->join('participantes as p', 'dap.participante_id', '=', 'p.id')
            ->whereBetween('af.fecha_firma', [
                Carbon::parse($desde)->startOfDay(),
                Carbon::parse($hasta)->endOfDay(),
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
            ->orderBy('af.fecha_firma', 'desc')
            ->get();

        // Codificación del logo en Base64 para dompdf
        $logoPath = public_path('img/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $fechaReporte = Carbon::now()->format('d/m/Y H:i:s');
        $usuario = Auth::user();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.reporte_registros', compact(
            'registros',
            'desde',
            'hasta',
            'columnas',
            'mapColumnas',
            'logoBase64',
            'fechaReporte',
            'usuario'
        ));

        $pdf->setPaper('letter', 'landscape');
        $pdf->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        $nombreArchivo = 'reporte-registros-' . $desde . '-al-' . $hasta . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Búsqueda AJAX: retorna clientes (representantes con firma) por nombre o cédula.
     */
    public function buscarCliente(Request $request)
    {
        if (!Auth::user()->tieneAccesoAdmin()) {
            abort(403);
        }

        $query = $request->input('q', '');

        if (strlen(trim($query)) < 2) {
            return response()->json([]);
        }

        $resultados = AcuerdoFirmado::with(['representante', 'participantes'])
            ->whereNotNull('firma_base64')
            ->whereHas('representante', function ($q) use ($query) {
                $q->where('nombre', 'LIKE', '%' . $query . '%')
                  ->orWhere('apellido', 'LIKE', '%' . $query . '%')
                  ->orWhere('cedula', 'LIKE', '%' . $query . '%');
            })
            ->latest('fecha_firma')
            ->limit(10)
            ->get()
            ->map(function ($acuerdo) {
                $rep = $acuerdo->representante;
                return [
                    'acuerdo_id'       => $acuerdo->id,
                    'nombre_completo'  => $rep->nombre_completo,
                    'cedula'           => $rep->cedula,
                    'telefono'         => $rep->telefono ?? '—',
                    'fecha_firma'      => $acuerdo->fecha_firma
                        ? Carbon::parse($acuerdo->fecha_firma)->format('d/m/Y H:i')
                        : '—',
                    'num_participantes' => $acuerdo->participantes->count(),
                    'pdf_url'          => route('admin.reportes.pdf', $acuerdo->id),
                ];
            });

        return response()->json($resultados);
    }

    /**
     * Genera el PDF del Acuerdo de Responsabilidad para un acuerdo específico.
     * Solo genera el PDF si existe una firma_base64 válida.
     */
    public function generarPdf(int $acuerdo_id)
    {
        if (!Auth::user()->tieneAccesoAdmin()) {
            abort(403, 'Acceso no autorizado.');
        }

        $firma = AcuerdoFirmado::with(['representante', 'participantes', 'terminos'])
            ->findOrFail($acuerdo_id);

        // Seguridad: No generar PDF sin firma válida
        if (empty($firma->firma_base64)) {
            abort(404, 'No existe una firma digital registrada para este acuerdo. El PDF no puede ser generado.');
        }

        $representante = $firma->representante;
        $participantes = $firma->participantes;

        // Cargar el texto legal exacto que fue aceptado en este acuerdo
        $terminos = $firma->terminos;
        if (!$terminos) {
            // Fallback: usar los términos activos actuales
            $terminos = TerminoCondicion::where('activo', true)->first();
        }
        $contenido_legal = $terminos ? $terminos->contenido : null;

        if ($contenido_legal) {
            // Eliminar la parte del consentimiento final solo para la exportación del PDF
            $pos = strpos($contenido_legal, '<strong>CONSENTIMIENTO DEL PADRE');
            if ($pos !== false) {
                $contenido_legal = preg_replace('/(<br\s*\/?>\s*)*<strong>CONSENTIMIENTO DEL PADRE.*/is', '', $contenido_legal);
            } else {
                $pos2 = strpos($contenido_legal, '• He leído, entiendo y acepto');
                if ($pos2 !== false) {
                    $contenido_legal = preg_replace('/(<br\s*\/?>\s*)*• He leído, entiendo y acepto.*/is', '', $contenido_legal);
                }
            }
        }

        // Construir el src de la imagen: puede ser base64 o una ruta física en disco
        $firma_base64_o_path = $firma->firma_base64;

        if ($firma_base64_o_path && Storage::disk('public')->exists($firma_base64_o_path)) {
            $fileContent = Storage::disk('public')->get($firma_base64_o_path);
            $firma_base64_o_path = 'data:image/png;base64,' . base64_encode($fileContent);
        } elseif ($firma_base64_o_path && !str_starts_with($firma_base64_o_path, 'data:')) {
            $firma_base64_o_path = 'data:image/png;base64,' . $firma_base64_o_path;
        }

        // Ruta absoluta al logo para dompdf (debe ser ruta del filesystem, no URL)
        $logoPath = public_path('img/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.acuerdo_responsabilidad', compact(
            'representante',
            'participantes',
            'firma',
            'firma_base64_o_path',
            'logoBase64',
            'contenido_legal'
        ));

        $pdf->setPaper('letter', 'portrait');

        // Optimización de memoria
        $pdf->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        $nombreArchivo = 'acuerdo-' . $representante->cedula . '-' . $acuerdo_id . '.pdf';

        return $pdf->stream($nombreArchivo);
    }
}
