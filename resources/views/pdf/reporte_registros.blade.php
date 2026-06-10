<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Registros</title>
    <style>
        @page {
            size: letter landscape;
            margin: 1.2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1F2937;
            margin: 0;
            padding: 0;
            font-size: 9px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo-cell {
            width: 150px;
        }
        .logo {
            height: 55px;
        }
        .title-cell {
            text-align: center;
        }
        .title-cell h1 {
            font-size: 16px;
            color: #4C1D95;
            margin: 0 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-cell p {
            font-size: 10px;
            color: #4B5563;
            margin: 0;
        }
        .meta-cell {
            width: 220px;
            text-align: right;
            font-size: 8.5px;
            color: #374151;
            line-height: 1.4;
        }
        .meta-cell strong {
            color: #1F2937;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #4C1D95;
            color: #FFFFFF;
            font-weight: bold;
            text-align: left;
            padding: 7px 6px;
            font-size: 8.5px;
            border: 1px solid #4C1D95;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 6px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 8px;
            color: #94A3B8;
            border-top: 1px solid #F1F5F9;
            padding-top: 5px;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="footer">
        <span>Ninja Park Kids &copy; {{ date('Y') }} — Reporte de Registros de Clientes — Generado por {{ $usuario->nombre }} — Página <span class="page-number"></span></span>
    </div>

    <!-- Encabezado Corporativo -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo" alt="Ninja Park Kids">
                @else
                    <span style="font-size: 16px; font-weight: bold; color: #4C1D95;">NINJA PARK</span>
                @endif
            </td>
            <td class="title-cell">
                <h1>Reporte de Registros</h1>
                <p>Sistema de Gestión Ninja Park Kids</p>
            </td>
            <td class="meta-cell">
                <strong>Fecha Reporte:</strong> {{ $fechaReporte }}<br>
                <strong>Rango:</strong> {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}<br>
                <strong>Generado por:</strong> {{ $usuario->nombre }} ({{ $usuario->rol->nombre_rol }})
            </td>
        </tr>
    </table>

    <!-- Tabla de datos -->
    <table class="data-table">
        <thead>
            <tr>
                @foreach($columnas as $col)
                    @if(isset($mapColumnas[$col]))
                        <th>{{ $mapColumnas[$col] }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $row)
                @php
                    $edadMenor = null;
                    if (in_array('edad_menor', $columnas) && $row->part_fnac) {
                        $edadMenor = \Carbon\Carbon::parse($row->part_fnac)->age . ' años';
                    }
                    $fechaFirma = $row->fecha_firma ? \Carbon\Carbon::parse($row->fecha_firma) : null;
                @endphp
                <tr>
                    @foreach($columnas as $col)
                        <td>
                            @switch($col)
                                @case('acuerdo_id')
                                    {{ $row->acuerdo_id }}
                                    @break
                                @case('rep_nombre')
                                    {{ trim($row->rep_nombre . ' ' . $row->rep_apellido) }}
                                    @break
                                @case('correo')
                                    {{ $row->correo ?? '—' }}
                                    @break
                                @case('telefono')
                                    {{ $row->telefono ?? '—' }}
                                    @break
                                @case('cedula')
                                    {{ $row->cedula }}
                                    @break
                                @case('rep_fnac')
                                    {{ $row->rep_fnac ? \Carbon\Carbon::parse($row->rep_fnac)->format('d/m/Y') : '—' }}
                                    @break
                                @case('parentesco')
                                    {{ $row->parentesco ?? '—' }}
                                    @break
                                @case('part_nombre')
                                    {{ trim($row->part_nombre . ' ' . $row->part_apellido) }}
                                    @break
                                @case('part_fnac')
                                    {{ $row->part_fnac ? \Carbon\Carbon::parse($row->part_fnac)->format('d/m/Y') : '—' }}
                                    @break
                                @case('edad_menor')
                                    {{ $edadMenor ?? '—' }}
                                    @break
                                @case('fecha_firma')
                                    {{ $fechaFirma ? $fechaFirma->format('d/m/Y') : '—' }}
                                    @break
                                @case('hora_firma')
                                    {{ $fechaFirma ? $fechaFirma->format('H:i:s') : '—' }}
                                    @break
                            @endswitch
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columnas) }}" style="text-align: center; color: #94A3B8; padding: 20px;">
                        No se encontraron registros para el rango de fechas seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
