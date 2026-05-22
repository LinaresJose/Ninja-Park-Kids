<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Acuerdo de Responsabilidad — Ninja Park</title>
    <style>
        /* ── Configuración de página ─────────────────────────────── */
        /* Reset de elementos básicos para no interferir con Dompdf */
        body,
        p,
        h1,
        h2,
        h3,
        h4,
        ul,
        ol,
        li,
        div {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: letter portrait;
            margin-top: 1.5cm;
            margin-bottom: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111111;
            background: #ffffff;
        }

        /* ── Encabezado con Logo ─────────────────────────────────── */
        .logo-container {
            position: absolute;
            top: -50px;
            right: 0px;
            width: 450px;
            text-align: right;
            z-index: -1;
            opacity: 0.80;
        }

        .logo-container img {
            max-height: 170px;
            max-width: 450px;
            display: block;
            margin-left: auto;
        }

        .doc-title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #111111;
            line-height: 1.3;
        }

        .doc-subtitle {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #444444;
            margin-top: 2px;
        }

        /* ── Párrafos del cuerpo ─────────────────────────────────── */
        .para {
            font-size: 11px;
            text-align: justify;
            margin-bottom: 8px;
        }

        .datos-rep {
            background: #f2f2f2;
            border-left: 3px solid #333333;
            padding: 5px 9px;
            margin: 6px 0;
            font-size: 11px;
        }

        .menores-lista {
            margin: 5px 0 5px 18px;
            padding: 0;
        }

        .menores-lista li {
            font-size: 11px;
            margin-bottom: 2px;
        }

        /* ── Contenido legal dinámico desde la BD ────────────────── */
        .legal-content {
            font-size: 11px;
            text-align: justify;
            margin-top: 8px;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        /* Normalizar el HTML que viene de la BD */
        .legal-content p {
            margin-bottom: 5px;
        }

        .legal-content ul {
            margin: 4px 0 4px 18px;
            padding: 0;
        }

        .legal-content ol {
            margin: 4px 0 4px 18px;
            padding: 0;
        }

        .legal-content li {
            margin-bottom: 2px;
            font-size: 11px;
        }

        .legal-content h1,
        .legal-content h2,
        .legal-content h3,
        .legal-content h4 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 7px;
            margin-bottom: 3px;
        }

        .legal-content strong {
            font-weight: bold;
        }

        .legal-content em {
            font-style: italic;
        }

        /* Separador de cláusulas estático (cuando no hay BD) */
        .clause-title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 7px;
            margin-bottom: 3px;
            color: #111111;
        }

        .clause-list {
            margin: 3px 0 4px 18px;
            padding: 0;
        }

        .clause-list li {
            font-size: 11px;
            margin-bottom: 2px;
            text-align: justify;
        }

        .consentimiento {
            font-size: 11px;
            text-align: justify;
            margin-top: 8px;
        }

        /* ── Sección de Firma ─────────────────────────────── */
        .firma-box {
            page-break-inside: avoid;
            margin-top: 18px;
            text-align: center;
        }

        .firma-titulo {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #555555;
            letter-spacing: 0.3px;
            margin-bottom: 8px;
        }

        .firma-img-wrap {
            display: block;
            margin-bottom: 4px;
        }

        .firma-img-wrap img {
            display: block;
            width: 220px;
            height: 90px;
            object-fit: contain;
            background: #ffffff;
            margin: 0 auto;
        }

        .firma-linea {
            width: 220px;
            border: none;
            border-top: 1px solid #333333;
            margin: 0 auto 4px auto;
        }

        .firma-datos {
            font-size: 10.5px;
            color: #222222;
            line-height: 1.6;
            margin-bottom: 5px;
        }

        .firma-fecha {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #555555;
            margin-top: 2px;
        }

        /* ── Pie de página ───────────────────────────────────────── */
        .page-footer {
            position: fixed;
            bottom: -1.5cm;
            left: 2cm;
            right: 2cm;
            text-align: center;
            font-size: 8.5px;
            color: #aaaaaa;
            border-top: 1px solid #eeeeee;
            padding-top: 3px;
        }
    </style>
</head>

<body>

    {{-- Pie de página fijo --}}
    <div class="page-footer">
        Ninja Park, C.A. — Valencia — Acuerdo de Responsabilidad — Documento generado electrónicamente
    </div>

    {{-- ── ENCABEZADO ─────────────────────────────────────────── --}}
    {{-- ── LOGO FLOTANTE A LA DERECHA ─────────────────────────── --}}
    @if($logoBase64)
        <div class="logo-container">
            <img src="{{ $logoBase64 }}" alt="Ninja Park Kids">
        </div>
    @endif


    {{-- ── TEXTO LEGAL DINÁMICO DESDE LA BASE DE DATOS ─────────── --}}
    @if($contenido_legal)
        <div class="legal-content">
            {!! $contenido_legal !!}
        </div>
    @else
        {{-- Fallback: cláusulas hardcoded si la BD no tiene contenido --}}
        <p class="clause-title">I. Participación Voluntaria</p>
        <p class="para">Yo, el Cliente, certifico que mi participación y/o la de mis representados en las instalaciones de
            Ninja Park es completamente voluntaria. Reconozco que tengo plena libertad para participar o no participar en
            las actividades ofrecidas. Al firmar este acuerdo, confirmo mi decisión voluntaria de participar.</p>

        <p class="clause-title">II. Reconocimiento de Reglas</p>
        <p class="para">El Cliente declara conocer y acepta acatar todas las reglas, normas de conducta y seguridad
            establecidas por Ninja Park. Entre las normas fundamentales se incluyen:</p>
        <ul class="clause-list">
            <li>No consumir alimentos ni bebidas dentro de las atracciones.</li>
            <li>Respetar los límites de edad, peso y estatura para cada atracción.</li>
            <li>Seguir en todo momento las instrucciones del personal de Ninja Park.</li>
            <li>No ingresar con objetos punzantes, vidrios, ni elementos peligrosos.</li>
            <li>Reportar inmediatamente cualquier lesión o accidente al personal del parque.</li>
        </ul>

        <p class="clause-title">III. Aceptación de Riesgo</p>
        <p class="para">El Cliente entiende y acepta expresamente que las actividades físicas y de entretenimiento extremo
            conllevan riesgos inherentes, incluyendo, sin limitación, caídas, colisiones, lesiones musculares y, en casos
            excepcionales, lesiones graves o la muerte. El Cliente asume voluntariamente todos estos riesgos conocidos y
            desconocidos.</p>

        <p class="clause-title">IV. Liberación y Descargo de Responsabilidad</p>
        <p class="para">A cambio de la participación permitida en las instalaciones de Ninja Park, el Cliente, en su nombre
            y en el de sus herederos, cesionarios y representantes legales, por medio del presente instrumento, libera,
            absuelve, descarga y renuncia a cualquier reclamación, acción, daño, pérdida o gasto contra Ninja Park, C.A.,
            sus socios, empleados, contratistas, y agentes, que surjan de o estén relacionados con la participación en las
            actividades del parque.</p>

        <p class="clause-title">V. Aptitud para Participar</p>
        <p class="para">El Cliente certifica que él y/o sus representados gozan de buena salud física y mental, y no padecen
            ninguna condición médica, lesión preexistente o discapacidad que pueda verse agravada por la participación en
            las actividades del parque.</p>

        <p class="clause-title">VI. Divulgación de Videos e Imágenes</p>
        <p class="para">El Cliente otorga su consentimiento expreso e irrevocable a Ninja Park, C.A. para fotografiar,
            filmar y/o grabar su imagen y la de sus representados durante la visita, así como para utilizar dicho material
            con fines publicitarios y comerciales.</p>
    @endif

    <div class="consentimiento">
        <strong>CONSENTIMIENTO DEL PADRE, REPRESENTANTE O RESPONSABLE</strong><br>
        (El Cliente)<br><br>

        <p class="para">En la ciudad de Naguanagua, Carabobo, a los
            {{ \Carbon\Carbon::parse($firma->fecha_firma)->format('d/m/Y') }}, yo,
            <strong>{{ $representante->nombre_completo }}</strong>, mayor de edad, titular de la Cédula de Identidad
            número <strong>{{ $representante->cedula }}</strong> y con número de contacto telefónico
            <strong>{{ $representante->telefono ?? '—' }}</strong>, actuando en pleno uso de mis facultades civiles,
            DECLARO EXPRESAMENTE:
        </p>

        <p class="para">Que he leído, comprendido y analizado en su totalidad el Acuerdo de Relevo de Responsabilidad de
            Ninja Park, C.A. descrito anteriormente. En consecuencia, manifiesto mi voluntad libre y soberana de
            adherirme a todos sus términos y condiciones, asumiendo la responsabilidad total por mi participación y la
            de los menores de edad que represento legalmente y que se detallan a continuación:</p>

        <p class="para">Participantes:</p>
        <ul class="clause-list">
            @foreach($participantes as $hijo)
                <li><strong>{{ $hijo->nombre_completo }}</strong>, en su condición de participante bajo mi tutela.</li>
            @endforeach
        </ul>

        <p class="para" style="margin-top: 8px;">Reconozco que el presente documento constituye un contrato vinculante y
            que mi firma electrónica, estampada al pie de este instrumento, ratifica mi compromiso de cumplir y hacer
            cumplir las normas de seguridad del parque, liberando a Ninja Park, C.A. de toda responsabilidad en los
            términos ya expuestos.</p>
    </div>


    {{-- ── FIRMA ─────────────────────────────────────────────── --}}
    <div class="firma-box">
        <div class="firma-titulo">Firma Digital del Cliente &mdash; Constancia de Aceptaci&oacute;n Electr&oacute;nica
        </div>
        <div class="firma-img-wrap">
            <img src="{{ $firma_base64_o_path }}" alt="Firma digital del representante">
        </div>
        <hr class="firma-linea">
        <div class="firma-datos">
            <strong>{{ $representante->nombre_completo }}</strong><br>
            C.I.: V/E-{{ $representante->cedula }}
        </div>
        <div class="firma-fecha">
            Acuerdo firmado electr&oacute;nicamente el d&iacute;a
            {{ \Carbon\Carbon::parse($firma->fecha_firma)->format('d/m/Y') }}
            a las
            {{ \Carbon\Carbon::parse($firma->fecha_firma)->format('H:i:s') }} (hora local).
        </div>
    </div>

</body>

</html>