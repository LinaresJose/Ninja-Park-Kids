<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Representante;
use App\Models\Participante;
use App\Models\TerminoCondicion;
use App\Models\AcuerdoFirmado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistroController extends Controller
{
    // 1. Muestra la pantalla inicial de solo cédula
    public function verificarIndex()
    {
        return view('registro.verificar');
    }

    // 2. Procesa la cédula y decide a dónde enviar al usuario
    public function consultarCedula(Request $request)
    {
        $request->validate([
            'cedula' => 'required|numeric|digits_between:7,8'
        ], [
            'cedula.required' => 'Debe ingresar su número de identificación para continuar.',
            'cedula.numeric' => 'La identificación debe contener solo números.',
            'cedula.digits_between' => 'La identificación debe tener entre 7 y 8 dígitos.'
        ]);

        $cedula = $request->cedula;
        
        // Buscamos si el representante ya existe
        $representante = Representante::where('cedula', $cedula)->first();

        if ($representante) {
            // Verificar si YA firmó hoy
            $acuerdoHoy = AcuerdoFirmado::where('representante_id', $representante->id)
                                        ->whereDate('fecha_firma', \Carbon\Carbon::today())
                                        ->first();

            if ($acuerdoHoy) {
                // YA TIENE PASE HOY: Muestra su pase existente
                return redirect()->route('registro.pase', $acuerdoHoy->id)
                                 ->with('info', 'Ya posee un pase vigente por el día de hoy.');
            }

            // NO HA FIRMADO HOY: Manda a la página de firma cargando sus niños
            return redirect()->route('registro.firma', $representante->id)
                             ->withInput()
                             ->with('info', '¡Bienvenido de nuevo! Seleccione los niños que ingresarán hoy y firme el acuerdo.');
        } else {
            // NO EXISTE: Manda al registro nuevo
            return redirect()->route('registro.nuevo', ['cedula' => $cedula])->withInput();
        }
    }

    // 3. Muestra el formulario de registro (Carga la cédula si viene de la verificación)
    public function index($cedula)
    {
        $termino = TerminoCondicion::where('activo', true)->first();
        return view('registro.registro', compact('cedula', 'termino'));
    }

    public function store(Request $request)
    {
        // Validamos los datos estrictamente (añadiendo parentesco, telefono y campos de niños faltantes en validación previa)
        $request->validate([
            'cedula'           => 'required|numeric|digits_between:7,8|unique:representantes,cedula',
            'nombre'           => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'apellido'         => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'parentesco'       => 'required|in:Padre,Madre,Representante Legal',
            'correo'           => 'required|email|max:100',
            'telefono'         => 'required|numeric|digits:11',
            'fecha_nacimiento' => 'required|date|before_or_equal:' . date('Y-m-d', strtotime('-18 years')), 
            'nombres_niños'    => 'required|array|min:1|max:15',
            'nombres_niños.*'  => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'apellidos_niños.*' => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'fechas_nacimiento_niños.*' => 'required|date|before:today',
            'firma_base64'     => 'required|string',
        ], [
            'cedula.required' => 'El número de identificación es obligatorio.',
            'cedula.numeric' => 'El número de identificación debe ser numérico.',
            'cedula.digits_between' => 'El número de identificación debe tener entre 7 y 8 dígitos.',
            'cedula.unique' => 'Este número de identificación ya está registrado.',
            'nombre.required' => 'El nombre del representante es obligatorio.',
            'nombre.regex' => 'El nombre del representante solo debe contener letras.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no debe exceder los 50 caracteres.',
            'apellido.required' => 'El apellido del representante es obligatorio.',
            'apellido.regex' => 'El apellido del representante solo debe contener letras.',
            'apellido.min' => 'El apellido debe tener al menos 2 caracteres.',
            'apellido.max' => 'El apellido no debe exceder los 50 caracteres.',
            'parentesco.required' => 'Debe seleccionar su relación con los menores.',
            'parentesco.in' => 'Seleccione una opción de parentesco válida.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingrese un formato de correo válido.',
            'correo.max' => 'El correo no debe exceder los 100 caracteres.',
            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.numeric' => 'El número de teléfono debe ser numérico.',
            'telefono.digits' => 'El número de teléfono debe tener exactamente 11 dígitos.',
            'fecha_nacimiento.required' => 'Su fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'El representante debe ser mayor de edad (mínimo 18 años). Verifique su fecha de nacimiento.',
            'nombres_niños.required' => 'Debe registrar al menos un niño.',
            'nombres_niños.min' => 'Debe registrar al menos un niño.',
            'nombres_niños.max' => 'No puede registrar más de 15 niños por representante.',
            'nombres_niños.*.required' => 'El nombre de cada niño es obligatorio.',
            'nombres_niños.*.regex' => 'El nombre de los niños solo debe contener letras.',
            'apellidos_niños.*.required' => 'El apellido de cada niño es obligatorio.',
            'apellidos_niños.*.regex' => 'El apellido de los niños solo debe contener letras.',
            'fechas_nacimiento_niños.*.required' => 'La fecha de nacimiento de cada niño es obligatoria.',
            'fechas_nacimiento_niños.*.before' => 'La fecha de nacimiento de los niños debe ser anterior a hoy.',
            'firma_base64.required' => 'La firma digital es obligatoria. Por favor, firme el acuerdo.',
            'firma_base64.string' => 'La firma digital enviada no tiene un formato válido.',
        ]);

        // Doble Verificación Manual de Edad (Por seguridad extrema)
        $fechaNacimiento = \Carbon\Carbon::parse($request->fecha_nacimiento);
        if ($fechaNacimiento->age < 18) {
            return back()->withErrors(['fecha_nacimiento' => 'El sistema ha detectado que es menor de edad. Los representantes deben tener al menos 18 años.'])->withInput();
        }

        $acuerdo = DB::transaction(function () use ($request) {
            // 1. Guardar Representante
            $rep = Representante::create([
                'cedula'           => $request->cedula,
                'nombre'           => $request->nombre,
                'apellido'         => $request->apellido,
                'parentesco'       => $request->parentesco,
                'correo'           => $request->correo, 
                'telefono'         => $request->telefono,
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ]);

            // 2. Guardar Niños y recolectar IDs
            $participantes_ids = [];
            if($request->has('nombres_niños')) {
                foreach ($request->nombres_niños as $key => $nombre_niño) {
                    $niño = Participante::create([
                        'representante_id' => $rep->id,
                        'nombre' => $nombre_niño,
                        'apellido' => $request->apellidos_niños[$key],
                        'fecha_nacimiento' => $request->fechas_nacimiento_niños[$key],
                    ]);
                    $participantes_ids[] = $niño->id;
                }
            }

            // 3. Obtener Término y Condición activo
            $termino = TerminoCondicion::where('activo', true)->firstOrFail();

            // 4. Crear el Acuerdo Firmado Inicial (pase del día)
            $acuerdo = AcuerdoFirmado::create([
                'representante_id' => $rep->id,
                'terminos_id'      => $termino->id,
                'fecha_firma'      => now(),
                'token_qr'         => (string) Str::uuid(), // UUID v4 único y seguro
                'firma_base64'     => $request->firma_base64
            ]);

            // 5. Vincular a los niños elegidos a este acuerdo específico
            $acuerdo->participantes()->sync($participantes_ids);

            return $acuerdo;
        });

        // Redirigir a la pantalla de Pase Exitoso
        return redirect()->route('registro.pase', $acuerdo->id)->with('success', '¡Acreditación exitosa! Bienvenid@ a Ninja Park.');
    }

    // 4. Muestra la pantalla de Firma para usuarios existentes
    public function firma($id)
    {
        $representante = Representante::with('participantes')->findOrFail($id);
        $termino = TerminoCondicion::where('activo', true)->first();
        return view('registro.firma', compact('representante', 'termino'));
    }

    // 5. Procesa la firma y actualiza participantes
    public function guardarFirma(Request $request, $id)
    {
        $request->validate([
            'nombres_niños.*'           => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'apellidos_niños.*'         => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'fechas_nacimiento_niños.*' => 'required|date|before:today',
            'participantes_existentes'  => 'array', 
            'aceptar_terminos'          => 'required|accepted',
            'firma_base64'              => 'required|string'
        ], [
            'nombres_niños.*.required'   => 'El nombre del nuevo participante es obligatorio.',
            'nombres_niños.*.regex'      => 'El nombre solo debe contener letras.',
            'apellidos_niños.*.required' => 'El apellido del nuevo participante es obligatorio.',
            'apellidos_niños.*.regex'    => 'El apellido solo debe contener letras.',
            'fechas_nacimiento_niños.*.required' => 'La fecha de nacimiento es obligatoria.',
            'fechas_nacimiento_niños.*.before'   => 'La fecha de nacimiento debe ser anterior a hoy.',
            'aceptar_terminos.accepted'  => 'Debe aceptar los términos de responsabilidad para continuar.',
            'firma_base64.required'      => 'La firma digital es obligatoria. Por favor, firme el acuerdo.',
            'firma_base64.string'        => 'La firma digital enviada no tiene un formato válido.',
        ]);

        // Validación Lógica: Al menos un niño seleccionado o creado, y máximo 15 totales
        $totalExistentesSelect = count($request->input('participantes_existentes', []));
        $totalNuevos = count($request->input('nombres_niños', []));
        $totalHoy = $totalExistentesSelect + $totalNuevos;

        if ($totalHoy == 0) {
            return back()->withErrors(['error_niños' => 'Debe seleccionar al menos un niño registrado o añadir uno nuevo para continuar.'])->withInput();
        }

        if ($totalHoy > 15) {
            return back()->withErrors(['error_niños' => 'Un representante solo puede registrar un máximo de 15 niños.'])->withInput();
        }

        $representante = Representante::findOrFail($id);

        $acuerdo = DB::transaction(function () use ($request, $representante) {
            $participantes_ids = [];

            // 1. Recoger los niños existentes que fueron seleccionados hoy
            if ($request->has('participantes_existentes')) {
                foreach ($request->participantes_existentes as $participante_id) {
                    // Validar que el niño pertenezca al representante
                    $existe = Participante::where('id', $participante_id)
                                          ->where('representante_id', $representante->id)
                                          ->first();
                    if ($existe) {
                        $participantes_ids[] = $existe->id;
                    }
                }
            }

            // 2. Guardar Niños Nuevos (si agregó alguno hoy)
            if($request->has('nombres_niños')) {
                foreach ($request->nombres_niños as $key => $nombre_niño) {
                    $nuevo_niño = Participante::create([
                        'representante_id' => $representante->id,
                        'nombre' => $nombre_niño,
                        'apellido' => $request->apellidos_niños[$key],
                        'fecha_nacimiento' => $request->fechas_nacimiento_niños[$key],
                    ]);
                    $participantes_ids[] = $nuevo_niño->id; // Este niño nuevo entra al parque hoy
                }
            }
            
            // 3. Obtener Término y Condición activo
            $termino = TerminoCondicion::where('activo', true)->firstOrFail();

            // 4. Crear el Acuerdo Firmado
            $acuerdo = AcuerdoFirmado::create([
                'representante_id' => $representante->id,
                'terminos_id' => $termino->id,
                'fecha_firma' => now(),
                'token_qr' => (string) Str::uuid(), // UUID v4 único y seguro
                'firma_base64' => $request->firma_base64
            ]);

            // 5. Vincular a los niños elegidos a este acuerdo específico
            $acuerdo->participantes()->sync($participantes_ids);

            return $acuerdo;
        });

        // Redirigir a la pantalla de Pase Exitoso
        return redirect()->route('registro.pase', $acuerdo->id)->with('success', '¡Acuerdo firmado y pase generado con éxito!');
    }

    // 6. Pantalla de Pase Exitoso
    public function pase($acuerdo_id)
    {
        $acuerdo = AcuerdoFirmado::with(['representante', 'participantes'])->findOrFail($acuerdo_id);
        return view('registro.pase', compact('acuerdo'));
    }

    // 7. Genera y sirve el cÃ³digo QR (formato SVG por compatibilidad con el servidor)
    public function qrImage($acuerdo_id)
    {
        $acuerdo = AcuerdoFirmado::findOrFail($acuerdo_id);

        $url = route('registro.validar', $acuerdo->token_qr);

        // SVG es mÃ¡s ligero y compatible cuando no hay imagick instalado
        $qrCode = QrCode::format('svg')
                        ->size(400)
                        ->margin(1)
                        ->color(123, 44, 191) // Ninja Purple
                        ->errorCorrection('H')
                        ->generate($url);

        $filename = "QR_" . strtoupper($acuerdo->representante->nombre) . "_" . strtoupper($acuerdo->representante->apellido) . ".svg";

        return response($qrCode)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Cache-Control', 'public, max-age=3600');
    }

    // 8. Endpoint de validación para el Operador Integral
    public function validarPase($token)
    {
        $acuerdo = AcuerdoFirmado::with(['representante', 'participantes'])
                                  ->where('token_qr', $token)
                                  ->first();

        if (!$acuerdo) {
            return view('registro.validar_pase', ['valido' => false, 'acuerdo' => null]);
        }

        // Comprobar si el pase es de hoy
        $vigente = \Carbon\Carbon::parse($acuerdo->fecha_firma)->isToday();

        return view('registro.validar_pase', compact('acuerdo', 'vigente'));
    }
}