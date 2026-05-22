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
use Maatwebsite\Excel\Facades\Excel;

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
        ]);

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $filename = 'ninja-park-registros_' . $desde . '_al_' . $hasta . '.xlsx';

        return Excel::download(new RegistrosExport($desde, $hasta), $filename);
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

        // Construir el src de la imagen: puede ser base64 o una ruta
        $firma_base64_o_path = $firma->firma_base64;

        // Si el valor guardado es solo base64 (sin el prefijo data:image), lo añadimos
        if (!str_starts_with($firma_base64_o_path, 'data:')) {
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
