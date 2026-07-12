<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRegistroRequest;
use App\Http\Requests\GuardarFirmaRequest;
use App\Services\RegistroService;
use App\Models\Representante;
use App\Models\AcuerdoFirmado;
use App\Models\TerminoCondicion;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistroController extends Controller
{
    public function __construct(protected RegistroService $registroService)
    {
    }

    /**
     * Pantalla inicial de verificación de cédula.
     */
    public function verificarIndex()
    {
        return view('registro.verificar');
    }

    /**
     * Procesa la cédula y redirige según el estado del representante.
     */
    public function consultarCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|numeric|digits_between:7,8',
        ], [
            'cedula.required'        => 'Debe ingresar su número de identificación para continuar.',
            'cedula.numeric'         => 'La identificación debe contener solo números.',
            'cedula.digits_between'  => 'La identificación debe tener entre 7 y 8 dígitos.',
        ]);

        $representante = Representante::where('cedula', $request->cedula)->first();

        if (!$representante) {
            return redirect()->route('registro.nuevo', ['cedula' => $request->cedula])->withInput();
        }

        $acuerdoHoy = AcuerdoFirmado::where('representante_id', $representante->id)
            ->whereDate('fecha_firma', Carbon::today())
            ->first();

        if ($acuerdoHoy) {
            return redirect()->route('registro.pase', $acuerdoHoy->id)
                ->with('info', 'Ya posee un pase vigente por el día de hoy.');
        }

        return redirect()->route('registro.firma', $representante->id)
            ->with('info', '¡Bienvenido de nuevo! Seleccione los niños que ingresarán hoy y firme el acuerdo.');
    }

    /**
     * Muestra el formulario de registro para un nuevo representante.
     */
    public function index($cedula)
    {
        $termino = TerminoCondicion::where('activo', true)->first();
        return view('registro.registro', compact('cedula', 'termino'));
    }

    /**
     * Guarda el nuevo representante con sus niños y firma el acuerdo.
     */
    public function store(StoreRegistroRequest $request)
    {
        // Doble verificación de edad (seguridad extra más allá de la validación de reglas)
        if (Carbon::parse($request->fecha_nacimiento)->age < 18) {
            return back()->withErrors([
                'fecha_nacimiento' => 'El sistema detectó que es menor de edad. El representante debe tener al menos 18 años.',
            ])->withInput();
        }

        // Crear representante con captura de excepción ante race condition
        try {
            $representante = Representante::create($request->only([
                'cedula', 'nombre', 'apellido', 'parentesco', 'correo', 'telefono', 'fecha_nacimiento',
            ]));
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                return back()->withErrors([
                    'cedula' => 'Este número de identificación ya se encuentra registrado por otro usuario.',
                ])->withInput();
            }
            throw $e;
        }

        // Construir array de niños nuevos con formato estándar
        $nuevosNiños = [];
        foreach ($request->input('nombres_niños', []) as $key => $nombre) {
            $nuevosNiños[] = [
                'nombre'           => $nombre,
                'apellido'         => $request->input("apellidos_niños.$key"),
                'fecha_nacimiento' => $request->input("fechas_nacimiento_niños.$key"),
            ];
        }

        try {
            $acuerdo = $this->registroService->crearAcuerdo($representante, [], $nuevosNiños, $request->firma_base64);
        } catch (\RuntimeException $e) {
            // Error de negocio conocido (ej: sin términos activos)
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            \Log::error('Error al crear acuerdo (store): ' . $e->getMessage(), [
                'representante_id' => $representante->id,
                'trace'            => $e->getTraceAsString(),
            ]);
            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado al procesar el acuerdo. Por favor, intente nuevamente.',
            ])->withInput();
        }

        return redirect()->route('registro.pase', $acuerdo->id)
            ->with('success', '¡Acreditación exitosa! Bienvenid@ a Ninja Park.');
    }

    /**
     * Muestra la pantalla de firma para representantes existentes.
     */
    public function firma($id)
    {
        $representante = Representante::with('participantes')->findOrFail($id);
        $termino       = TerminoCondicion::where('activo', true)->first();
        return view('registro.firma', compact('representante', 'termino'));
    }

    /**
     * Procesa la firma y actualiza los participantes del día.
     */
    public function guardarFirma(GuardarFirmaRequest $request, $id)
    {
        $representante = Representante::findOrFail($id);

        // Filtrar solo los IDs de participantes que pertenecen al representante
        $participantesIds = $this->registroService->filtrarParticipantesValidos(
            $representante,
            $request->input('participantes_existentes', [])
        );

        // Construir array de niños nuevos
        $nuevosNiños = [];
        foreach ($request->input('nombres_niños', []) as $key => $nombre) {
            $nuevosNiños[] = [
                'nombre'           => $nombre,
                'apellido'         => $request->input("apellidos_niños.$key"),
                'fecha_nacimiento' => $request->input("fechas_nacimiento_niños.$key"),
            ];
        }

        try {
            $acuerdo = $this->registroService->crearAcuerdo($representante, $participantesIds, $nuevosNiños, $request->firma_base64);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            \Log::error('Error al guardar firma: ' . $e->getMessage(), [
                'representante_id' => $representante->id,
                'trace'            => $e->getTraceAsString(),
            ]);
            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado al guardar la firma. Por favor, intente nuevamente.',
            ])->withInput();
        }

        return redirect()->route('registro.pase', $acuerdo->id)
            ->with('success', '¡Acuerdo firmado y pase generado con éxito!');
    }

    /**
     * Pantalla de pase exitoso con QR.
     */
    public function pase($acuerdo_id)
    {
        $acuerdo = AcuerdoFirmado::with(['representante', 'participantes'])->findOrFail($acuerdo_id);
        return view('registro.pase', compact('acuerdo'));
    }

    /**
     * Genera y sirve el código QR en SVG.
     */
    public function qrImage($acuerdo_id)
    {
        $acuerdo = AcuerdoFirmado::with('representante')->findOrFail($acuerdo_id);
        $url     = route('registro.validar', $acuerdo->token_qr);

        $qrCode   = QrCode::format('svg')->size(400)->margin(1)->color(123, 44, 191)->errorCorrection('H')->generate($url);
        $nombre   = \Illuminate\Support\Str::ascii($acuerdo->representante->nombre);
        $apellido = \Illuminate\Support\Str::ascii($acuerdo->representante->apellido);
        $filename = 'QR_' . strtoupper($nombre) . '_' . strtoupper($apellido) . '.svg';
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename); // Sanitizar caracteres no-ASCII

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Endpoint de validación del QR para el operador de puerta.
     */
    public function validarPase($token)
    {
        $acuerdo = AcuerdoFirmado::with(['representante', 'participantes'])
            ->where('token_qr', $token)
            ->first();

        if (!$acuerdo) {
            return view('registro.validar_pase', ['valido' => false, 'acuerdo' => null]);
        }

        $vigente = Carbon::parse($acuerdo->fecha_firma)->isToday();
        return view('registro.validar_pase', compact('acuerdo', 'vigente'));
    }
}