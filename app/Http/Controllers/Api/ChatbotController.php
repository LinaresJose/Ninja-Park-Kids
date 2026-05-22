<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Representante;
use App\Models\Tarifa;
use App\Models\Promocion;
use App\Models\HorarioParque;
use App\Models\AcuerdoFirmado;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    // ── INTENT MAPS ──────────────────────────────────────────────────
    private array $publicIntents = [
        'saludo'         => ['hola', 'buenos', 'buenas', 'hey', 'saludos', 'qué tal', 'que tal'],
        'despedida'      => ['gracias', 'adios', 'adiós', 'chao', 'bye', 'hasta luego', 'nos vemos'],
        'tarifas'        => ['tarifa', 'precio', 'costo', 'cuánto cuesta', 'cuanto cuesta', 'cobran', 'vale', 'cuánto es', 'cuanto es'],
        'horarios'       => ['horario', 'hora', 'abierto', 'abren', 'cierran', 'cuándo', 'cuando'],
        'promociones'    => ['promo', 'promocion', 'promoción', 'descuento', 'oferta', 'especial', 'rebaja'],
        'redes_sociales' => ['instagram', ' ig', 'redes', 'facebook', 'social', 'ninjapark', 'seguir'],
        'reservas'       => ['reservar', 'reserva', 'cumpleaños', 'cumpleanos', 'cumple', 'evento', 'fiesta', 'party'],
    ];

    private array $staffIntents = [
        'afluencia'      => ['afluencia', 'visitas', 'visitantes', 'cuántos', 'cuantos', 'asistencia', 'personas', 'entradas', 'ingresaron'],
        'horas_trafico'  => ['tráfico', 'trafico', 'pico', 'horas pico', 'concurrido', 'más lleno', 'mas lleno', 'mayor tráfico'],
        'buscar_cliente' => ['buscar', 'cliente', 'representante', 'información de', 'informacion de', 'datos de', 'historial'],
    ];

    // ── HELPERS ───────────────────────────────────────────────────────
    private function detectIntent(string $msg, array $map): string
    {
        $lower = strtolower(trim($msg));
        foreach ($map as $intent => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lower, $kw)) return $intent;
            }
        }
        return 'fallback';
    }

    private function containsCedula(string $msg): ?string
    {
        return preg_match('/\b(\d{6,10})\b/', $msg, $m) ? $m[1] : null;
    }

    private function extractPeriod(string $msg): string
    {
        $lower = strtolower($msg);
        if (str_contains($lower, 'mes') || str_contains($lower, '30')) return 'mes';
        if (str_contains($lower, 'semana') || str_contains($lower, '7')) return 'semana';
        return 'dia';
    }

    // ── ENDPOINTS PÚBLICOS ────────────────────────────────────────────
    public function verificarCedula(Request $request)
    {
        $cedula = preg_replace('/\D/', '', $request->input('cedula', ''));
        if (strlen($cedula) < 6) {
            return response()->json(['status' => 'error', 'message' => 'Por favor, ingresa una cédula válida (mínimo 6 dígitos).']);
        }

        $rep = Representante::where('cedula', $cedula)->first();

        if ($rep) {
            return response()->json([
                'status'       => 'found',
                'nombre'       => $rep->nombre,
                'message'      => "¡Hola, **{$rep->nombre}**! 👋 Me alegra verte de nuevo en Ninja Park. ¿En qué puedo ayudarte?",
                'quickReplies' => ['Ver Tarifas', 'Ver Horarios', 'Promociones', 'Reservar'],
            ]);
        }

        return response()->json([
            'status'  => 'not_found',
            'message' => "¡Ups! 📋 No encuentro tu cédula en el sistema. Parece que eres nuevo por aquí. ¡Bienvenido! Puedes registrarte en el sistema tocando el botón de abajo.",
            'link'    => ['url' => url('/'), 'label' => '📝 Completar Registro'],
        ]);
    }

    public function chat(Request $request)
    {
        $message = trim($request->input('message', ''));
        if (!$message) {
            return response()->json([
                'message'      => '¿En qué te puedo ayudar? 🥷',
                'quickReplies' => ['Ver Tarifas', 'Ver Horarios', 'Promociones', 'Reservar'],
            ]);
        }

        if ($cedula = $this->containsCedula($message)) {
            return $this->verificarCedula(new Request(['cedula' => $cedula]));
        }

        return match ($this->detectIntent($message, $this->publicIntents)) {
            'saludo'         => $this->respondSaludo(),
            'despedida'      => $this->respondDespedida(),
            'tarifas'        => $this->respondTarifas(),
            'horarios'       => $this->respondHorarios(),
            'promociones'    => $this->respondPromociones(),
            'redes_sociales' => $this->respondRedes(),
            'reservas'       => $this->respondReservas(),
            default          => $this->respondFallback(),
        };
    }

    // ── ENDPOINT STAFF (requiere auth) ────────────────────────────────
    public function chatStaff(Request $request)
    {
        $message = trim($request->input('message', ''));
        if (!$message) {
            return response()->json(['message' => 'Ingresa una consulta. 📊']);
        }

        $intent = $this->detectIntent($message, $this->staffIntents);
        if ($intent === 'fallback') {
            $intent = $this->detectIntent($message, $this->publicIntents);
        }

        return match ($intent) {
            'afluencia'      => $this->respondAfluencia($message),
            'horas_trafico'  => $this->respondHorasTrafico($message),
            'buscar_cliente' => $this->respondBuscarCliente($message),
            'tarifas'        => $this->respondTarifas(),
            'horarios'       => $this->respondHorarios(),
            'promociones'    => $this->respondPromociones(),
            default          => $this->respondFallbackStaff(),
        };
    }

    // ── RESPUESTAS PÚBLICAS ───────────────────────────────────────────
    private function respondSaludo()
    {
        return response()->json([
            'message'      => "¡Hola! Soy **Malochy**, tu asistente ninja 🥷\n¿En qué puedo ayudarte hoy?",
            'quickReplies' => ['Ver Tarifas', 'Ver Horarios', 'Promociones', 'Reservar', 'Redes Sociales'],
        ]);
    }

    private function respondDespedida()
    {
        return response()->json([
            'message' => '¡Hasta pronto! 👋 Fue un placer ayudarte. ¡Te esperamos en Ninja Park Kids! 🥷⚡',
        ]);
    }

    private function respondTarifas()
    {
        $tarifas = Tarifa::where('esta_activa', true)->get();
        if ($tarifas->isEmpty()) {
            return response()->json(['message' => 'En este momento no tenemos tarifas configuradas. ¡Contáctanos para más info!']);
        }
        $lines = $tarifas->map(fn($t) => "• **{$t->nombre_tarifa}** ({$t->duracion_minutos} min) → 💲 {$t->precio}")->join("\n");
        return response()->json([
            'message'      => "🎯 **Nuestras Tarifas:**\n\n{$lines}",
            'quickReplies' => ['Ver Horarios', 'Reservar'],
        ]);
    }

    private function respondHorarios()
    {
        $horarios = HorarioParque::orderBy('dia_semana')->get();
        if ($horarios->isEmpty()) {
            return response()->json(['message' => 'Los horarios aún no están configurados. ¡Síguenos en Instagram para novedades!']);
        }
        $dias  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $lines = $horarios->map(function ($h) use ($dias) {
            $dia = $dias[$h->dia_semana] ?? "Día {$h->dia_semana}";
            if ($h->esta_cerrado) return "• **{$dia}** → 🔴 Cerrado";
            return "• **{$dia}** → 🟢 " . substr($h->hora_apertura, 0, 5) . ' – ' . substr($h->hora_cierre, 0, 5);
        })->join("\n");
        return response()->json([
            'message'      => "🕐 **Horarios del Parque:**\n\n{$lines}",
            'quickReplies' => ['Ver Tarifas', 'Reservar'],
        ]);
    }

    private function respondPromociones()
    {
        $promos = Promocion::where('esta_activa', true)
            ->whereDate('fecha_fin', '>=', now())
            ->whereDate('fecha_inicio', '<=', now())
            ->get();

        if ($promos->isEmpty()) {
            return response()->json([
                'message'      => "😅 No hay promociones activas ahora mismo.\n¡Síguenos en Instagram para enterarte primero de todas las novedades!",
                'quickReplies' => ['Ver Tarifas', 'Redes Sociales'],
            ]);
        }

        $lines = $promos->map(fn($p) =>
            "🏷️ **{$p->titulo}**\n   {$p->descripcion_detallada}\n   💲 {$p->precio_especial} | Hasta: " . Carbon::parse($p->fecha_fin)->format('d/m/Y')
        )->join("\n\n");

        return response()->json([
            'message'      => "🎉 **Promociones Vigentes:**\n\n{$lines}",
            'quickReplies' => ['Ver Tarifas', 'Reservar'],
        ]);
    }

    private function respondRedes()
    {
        return response()->json([
            'message' => '📸 ¡Síguenos en Instagram! Publicamos contenido y novedades del parque constantemente.',
            'link'    => ['url' => 'https://www.instagram.com/ninjapark_ve/', 'label' => '📸 @ninjapark_ve en Instagram'],
        ]);
    }

    private function respondReservas()
    {
        return response()->json([
            'message' => '🎂 ¿Quieres reservar para un cumpleaños o evento especial? ¡Contáctanos directamente!',
            'link'    => ['url' => 'https://linktr.ee/ninjapark_ve?utm_source=ig&utm_medium=social&utm_content=link_in_bio&fbclid=PAZXh0bgNhZW0CMTEAc3J0YwZhcHBfaWQMMjU2MjgxMDQwNTU4AAGn6IuS0rPQnM16DHluOdZTYBzvTkjM2CMMgVq_l8X4gj3lU9rYq7lz7ii34Tg_aem_r5VYh0C5Wcc9NMbwAvGHiQ', 'label' => '🔗 Reservar vía Linktree'],
        ]);
    }

    private function respondFallback()
    {
        return response()->json([
            'message'      => "¡Hmm! No estoy seguro de entender 🤔\nPuedo ayudarte con tarifas, horarios, promociones, redes sociales o reservaciones.",
            'quickReplies' => ['Ver Tarifas', 'Ver Horarios', 'Promociones', 'Reservar', 'Redes Sociales'],
        ]);
    }

    // ── RESPUESTAS STAFF ──────────────────────────────────────────────
    private function respondAfluencia(string $message)
    {
        $period = $this->extractPeriod($message);
        [$baseQuery, $label] = match ($period) {
            'mes'    => [AcuerdoFirmado::whereDate('fecha_firma', '>=', now()->subDays(30)), 'Últimos 30 días'],
            'semana' => [AcuerdoFirmado::whereDate('fecha_firma', '>=', now()->subDays(7)),  'Últimos 7 días'],
            default  => [AcuerdoFirmado::whereDate('fecha_firma', now()),                      'Hoy'],
        };

        $total = (clone $baseQuery)->count();
        $byDay = (clone $baseQuery)
            ->select(DB::raw('DATE(fecha_firma) as dia'), DB::raw('COUNT(*) as total'))
            ->groupBy('dia')
            ->orderBy('dia', 'desc')
            ->get();

        $lines = $byDay->isEmpty()
            ? '• Sin registros en este período.'
            : $byDay->map(fn($r) => "• **{$r->dia}** → {$r->total} " . ($r->total === 1 ? 'visita' : 'visitas'))->join("\n");

        return response()->json([
            'message' => "📊 **Afluencia — {$label}:**\n\n{$lines}\n\n🔢 **Total: {$total} " . ($total === 1 ? 'visita' : 'visitas') . '**',
        ]);
    }

    private function respondHorasTrafico(string $message)
    {
        $period = $this->extractPeriod($message);
        [$baseQuery, $label] = match ($period) {
            'mes'    => [AcuerdoFirmado::whereDate('fecha_firma', '>=', now()->subDays(30)), 'Últimos 30 días'],
            'semana' => [AcuerdoFirmado::whereDate('fecha_firma', '>=', now()->subDays(7)),  'Últimos 7 días'],
            default  => [AcuerdoFirmado::whereDate('fecha_firma', now()),                      'Hoy'],
        };

        $byHour = $baseQuery
            ->select(DB::raw('HOUR(fecha_firma) as hora'), DB::raw('COUNT(*) as total'))
            ->groupBy('hora')
            ->orderBy('total', 'desc')
            ->get();

        if ($byHour->isEmpty()) {
            return response()->json(['message' => "⏰ No hay registros de tráfico para **{$label}**."]);
        }

        $maxVal = $byHour->max('total');
        $lines  = $byHour->map(function ($r) use ($maxVal) {
            $h   = str_pad($r->hora, 2, '0', STR_PAD_LEFT);
            $bar = str_repeat('█', (int) max(1, round($r->total / max(1, $maxVal) * 8)));
            return "• **{$h}:00** hs  {$bar} {$r->total}";
        })->join("\n");

        $peak    = $byHour->first();
        $peakStr = str_pad($peak->hora, 2, '0', STR_PAD_LEFT);

        return response()->json([
            'message' => "⏰ **Horas Pico — {$label}:**\n\n{$lines}\n\n🔥 **Mayor tráfico: {$peakStr}:00 hs — {$peak->total} ingresos**",
        ]);
    }

    private function respondBuscarCliente(string $message)
    {
        $cedula = $this->containsCedula($message);
        $rep    = null;

        if ($cedula) {
            $rep = Representante::where('cedula', $cedula)->first();
        } else {
            $cleaned = preg_replace('/\b(buscar|cliente|representante|información|informacion|datos|historial|de|del|la|el)\b/i', '', $message);
            $term    = trim(preg_replace('/\s+/', ' ', $cleaned));
            if (strlen($term) >= 3) {
                $rep = Representante::where('nombre', 'LIKE', "%{$term}%")
                    ->orWhere('apellido', 'LIKE', "%{$term}%")
                    ->first();
            }
        }

        if (!$rep) {
            return response()->json(['message' => '🔍 No encontré ningún cliente con esa información. Intenta con la cédula completa o el nombre/apellido.']);
        }

        $visitas      = AcuerdoFirmado::where('representante_id', $rep->id)->count();
        $ultimaFirma  = AcuerdoFirmado::where('representante_id', $rep->id)->orderBy('fecha_firma', 'desc')->value('fecha_firma');
        $ultimaStr    = $ultimaFirma ? Carbon::parse($ultimaFirma)->format('d/m/Y H:i') : 'N/A';

        $menores    = $rep->participantes()->get();
        $menoresStr = $menores->isEmpty()
            ? 'Ninguno registrado'
            : $menores->map(fn($m) => "   • {$m->nombre_completo} ({$m->edad} años)")->join("\n");

        return response()->json([
            'message' =>
                "👤 **Perfil del Cliente:**\n\n" .
                "**Nombre:** {$rep->nombre_completo}\n" .
                "**Cédula:** {$rep->cedula}\n" .
                "**Teléfono:** {$rep->telefono}\n" .
                "**Correo:** {$rep->correo}\n\n" .
                "📅 **Visitas registradas:** {$visitas}\n" .
                "🕐 **Última visita:** {$ultimaStr}\n\n" .
                "👧 **Menores a cargo:**\n{$menoresStr}",
        ]);
    }

    private function respondFallbackStaff()
    {
        return response()->json([
            'message'      => "🤖 Soy **Malochy** en modo Staff. Puedo ayudarte con:\n\n• 📊 Afluencia (hoy / semana / mes)\n• ⏰ Horas de mayor tráfico\n• 🔍 Buscar cliente por nombre o cédula\n• 📋 Tarifas, horarios y promociones",
            'quickReplies' => ['Afluencia hoy', 'Afluencia esta semana', 'Horas pico', 'Buscar cliente'],
        ]);
    }
}
