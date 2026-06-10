@extends('diseños.staff')

@section('title', 'Reportes y Descargas | Ninja Park')
@section('title_header', 'Reportes y Descargas')

@push('styles')
<style>
.section-label {
    font-family: 'Montserrat', sans-serif;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
    margin-bottom: 1.25rem;
}
.date-field { position: relative; }
.date-field label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 0.4rem;
    font-family: 'Montserrat', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.date-field i.ico {
    position: absolute;
    left: 0.75rem;
    bottom: 0.7rem;
    color: var(--primary);
    font-size: 1rem;
    pointer-events: none;
}
.date-field input {
    width: 100%;
    padding: 0.6rem 1rem 0.6rem 2.4rem;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    font-size: 0.88rem;
    color: var(--text-main);
    background: #F8FAFC;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.date-field input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(109,40,217,.1);
    background: #fff;
}
.btn-primary-ninja {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.65rem 1.4rem;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: background .2s, transform .15s, box-shadow .2s;
}
.btn-primary-ninja:hover {
    background: var(--primary-hover);
    box-shadow: 0 4px 14px rgba(109,40,217,.35);
    transform: translateY(-1px);
    color:#fff;
}
.btn-primary-ninja:disabled {
    background: #A78BFA;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}
.spin-hide { display: none; }
.is-loading .spin-show { display: none; }
.is-loading .spin-hide { display: inline-block; }
@keyframes spin-anim { to { transform: rotate(360deg); } }
.btn-spinner {
    width: 15px; height: 15px;
    border: 2.5px solid rgba(255,255,255,.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin-anim .7s linear infinite;
}
.search-box { position: relative; max-width: 520px; }
.search-box i.ico {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary);
    font-size: 1.05rem;
    pointer-events: none;
}
.search-box input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.7rem;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    color: var(--text-main);
    background: #F8FAFC;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.search-box input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(109,40,217,.1);
    background: #fff;
}
.result-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.9rem 1.2rem;
    border: 1px solid #F1F5F9;
    border-radius: 10px;
    background: #FAFBFF;
    margin-bottom: 0.6rem;
    gap: 1rem;
    transition: box-shadow .2s, border-color .2s;
}
.result-card:hover { box-shadow: 0 4px 12px rgba(109,40,217,.08); border-color: #C4B5FD; }
.rc-name {
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 0.93rem;
    color: var(--text-main);
}
.rc-meta { font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem; }
.btn-pdf {
    background: #EDE9FE;
    color: var(--primary);
    border: 1.5px solid #C4B5FD;
    border-radius: 7px;
    padding: 0.42rem 0.9rem;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 0.78rem;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    text-decoration: none;
    flex-shrink: 0;
    transition: background .2s, box-shadow .2s;
}
.btn-pdf:hover { background: var(--primary); color:#fff; box-shadow: 0 3px 10px rgba(109,40,217,.25); border-color: var(--primary); }
.estado { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); }
.estado i { font-size: 2.2rem; display: block; margin-bottom: .6rem; opacity: .35; }
.inline-alert {
    border-radius: 8px;
    font-size: 0.82rem;
    padding: 0.6rem 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    margin-top: .75rem;
}
.alert-ok  { background: var(--badge-success-bg); color: var(--badge-success-text); }
.alert-err { background: var(--badge-error-bg);   color: var(--badge-error-text); }
.btn-pdf-ninja {
    background: #DC2626;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0.65rem 1.4rem;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: background .2s, transform .15s, box-shadow .2s;
}
.btn-pdf-ninja:hover {
    background: #B91C1C;
    box-shadow: 0 4px 14px rgba(220,38,38,.35);
    transform: translateY(-1px);
    color: #fff;
}
.btn-pdf-ninja:disabled {
    background: #FCA5A5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}
.btn-pdf-ninja .btn-spinner {
    width: 15px; height: 15px;
    border: 2.5px solid rgba(255,255,255,.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin-anim .7s linear infinite;
}
.transition-transform {
    transition: transform 0.25s ease;
}
/* Panel de columnas con animación smooth via max-height */
#panelColumnas {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease, opacity 0.3s ease, margin-top 0.3s ease;
    opacity: 0;
    margin-top: 0;
}
#panelColumnas.panel-open {
    max-height: 600px;
    opacity: 1;
    margin-top: 1.25rem;
}
/* Estilo del botón toggle de columnas */
#btnToggleColumnas {
    background: transparent;
    color: #6B7280;
    border: 1.5px dashed #CBD5E1;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    cursor: pointer;
    transition: all 0.2s;
    height: 38px;
}
#btnToggleColumnas:hover {
    background: #F1F5F9;
    border-color: var(--primary);
    color: var(--primary);
}
#btnToggleColumnas.activo {
    background: #EDE9FE;
    border-color: var(--primary);
    border-style: solid;
    color: var(--primary);
}
#btnToggleColumnas #collapseIcon {
    transition: transform 0.25s ease;
}
#btnToggleColumnas.activo #collapseIcon {
    transform: rotate(180deg);
}
</style>
@endpush

@section('content')

{{-- ── SECCIÓN 1: REPORTE DINÁMICO ───────────────────────────── --}}
<div class="card-modern mb-4">
    <p class="section-label"><i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>Reporte Dinámico de Registros</p>
    <h5 class="font-title mb-1" style="font-size:1.05rem;">Descargar Reportes (Excel / PDF)</h5>
    <p class="text-muted mb-4" style="font-size:.83rem;max-width:560px;">
        Genera reportes masivos de representantes, menores y firmas, filtrado por fechas. Selecciona qué columnas exportar y escoge el formato.
    </p>

    <form id="frmExcel" method="GET" target="_blank">
        <!-- Rango de Fechas -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6">
                <div class="date-field">
                    <label for="fpDesde">Desde</label>
                    <i class="bi bi-calendar-event ico"></i>
                    <input type="text" id="fpDesde" name="desde" placeholder="Selecciona fecha" autocomplete="off" required>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="date-field">
                    <label for="fpHasta">Hasta</label>
                    <i class="bi bi-calendar-event ico"></i>
                    <input type="text" id="fpHasta" name="hasta" placeholder="Selecciona fecha" autocomplete="off" required>
                </div>
            </div>
        </div>

        <!-- Barra de Acciones: Botones de Descarga + Toggle de Columnas -->
        <div class="d-flex align-items-center gap-2 flex-wrap mb-0">
            <!-- Descargar Excel -->
            <button type="button" class="btn-primary-ninja" id="btnExcel">
                <span class="btn-spinner spin-hide"></span>
                <i class="bi bi-file-earmark-excel-fill spin-show"></i>
                <span class="spin-show">Descargar Excel</span>
                <span class="spin-hide">Generando Excel...</span>
            </button>

            <!-- Descargar PDF -->
            <button type="button" class="btn-pdf-ninja" id="btnPdfBulk">
                <span class="btn-spinner spin-hide"></span>
                <i class="bi bi-file-earmark-pdf-fill spin-show"></i>
                <span class="spin-show">Descargar PDF</span>
                <span class="spin-hide">Generando PDF...</span>
            </button>

            <!-- Separador visual -->
            <div style="width: 1px; height: 30px; background: #E2E8F0;" class="d-none d-sm-block"></div>

            <!-- Toggle Personalizar Columnas (JS puro, sin Bootstrap Collapse) -->
            <button type="button" id="btnToggleColumnas">
                <i class="bi bi-sliders2"></i>
                <span>Personalizar Columnas</span>
                <i class="bi bi-chevron-down" id="collapseIcon"></i>
            </button>
        </div>

        <div id="alertaExcel" class="mt-3"></div>

        <!-- Panel de Columnas (animación max-height por JS) -->
        <div id="panelColumnas">
            <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 1.1rem 1.25rem;">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <span class="fw-bold font-title" style="font-size: 0.78rem; letter-spacing: 0.06em; color: var(--text-muted); text-transform: uppercase;">
                        <i class="bi bi-layout-three-columns me-1" style="color: var(--primary);"></i> Columnas del Reporte
                    </span>
                    <div class="d-flex gap-3">
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold" style="font-size: 0.72rem; color: var(--primary);" id="btnSelectAllCols">Marcar todas</button>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold" style="font-size: 0.72rem; color: var(--text-muted);" id="btnDeselectAllCols">Desmarcar todas</button>
                    </div>
                </div>
                <div class="row g-2">
                    @foreach([
                        'acuerdo_id' => 'ID Acuerdo',
                        'rep_nombre' => 'Nombre Representante',
                        'correo' => 'Correo Electrónico',
                        'telefono' => 'Teléfono',
                        'cedula' => 'Cédula Identidad',
                        'rep_fnac' => 'F. Nac. Rep.',
                        'parentesco' => 'Parentesco',
                        'part_nombre' => 'Nombre del Menor',
                        'part_fnac' => 'F. Nac. Menor',
                        'edad_menor' => 'Edad del Menor',
                        'fecha_firma' => 'Fecha de Firma',
                        'hora_firma' => 'Hora de Firma'
                    ] as $key => $label)
                    <div class="col-6 col-sm-4 col-xl-3">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="columnas[]" value="{{ $key }}" id="col_{{$key}}" checked>
                            <label class="form-check-label small fw-semibold" for="col_{{$key}}" style="cursor: pointer; color: #374151;">{{ $label }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </form>
</div>

{{-- ── SECCIÓN 2: PDF ───────────────────────────────────────── --}}
<div class="card-modern">
    <p class="section-label"><i class="bi bi-file-earmark-pdf-fill me-1"></i>Acuerdo de Responsabilidad Individual</p>
    <h5 class="font-title mb-1" style="font-size:1.05rem;">Buscar Cliente y Generar PDF</h5>
    <p class="text-muted mb-4" style="font-size:.83rem;max-width:560px;">
        Localiza un cliente por nombre o cédula y genera su Acuerdo de Responsabilidad con firma digital embebida.
    </p>

    <div class="search-box">
        <i class="bi bi-search ico"></i>
        <input type="text" id="inputBusqueda" placeholder="Buscar por nombre o cédula…" autocomplete="off">
    </div>

    <div id="resultadosBusqueda">
        <div class="estado">
            <i class="bi bi-person-lines-fill"></i>
            <span>Escribe al menos 2 caracteres para buscar.</span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/flatpickr/js/flatpickr.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Flatpickr ────────────────────────────────────────────
    const locale = {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
            longhand:  ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado']
        },
        months: {
            shorthand: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
            longhand:  ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
        }
    };

    const fpD = flatpickr('#fpDesde', {
        dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y',
        maxDate: 'today', locale,
        onChange: d => { if (d[0]) fpH.set('minDate', d[0]); }
    });
    const fpH = flatpickr('#fpHasta', {
        dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y',
        maxDate: 'today', locale
    });

    // ── Descarga Dinámica (Excel & PDF) ───────────────────────
    const frmExcel   = document.getElementById('frmExcel');

    // ── Toggle Panel Columnas (JS puro, sin Bootstrap Collapse) ──
    const btnToggle  = document.getElementById('btnToggleColumnas');
    const panelCols  = document.getElementById('panelColumnas');
    btnToggle.addEventListener('click', function() {
        const abierto = panelCols.classList.contains('panel-open');
        panelCols.classList.toggle('panel-open', !abierto);
        btnToggle.classList.toggle('activo', !abierto);
    });
    const btnExcel   = document.getElementById('btnExcel');
    const btnPdfBulk = document.getElementById('btnPdfBulk');
    const alertaDiv  = document.getElementById('alertaExcel');

    // Manejo de switches para seleccionar/deseleccionar todas
    const checkboxes = document.querySelectorAll('input[name="columnas[]"]');
    document.getElementById('btnSelectAllCols').addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = true);
    });
    document.getElementById('btnDeselectAllCols').addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false);
    });

    function validateDates() {
        const desde = document.getElementById('fpDesde').value;
        const hasta = document.getElementById('fpHasta').value;
        if (!desde || !hasta) {
            alertaDiv.innerHTML = '<div class="inline-alert alert-err"><i class="bi bi-exclamation-triangle-fill"></i> Selecciona ambas fechas antes de descargar.</div>';
            return false;
        }
        // Validar que al menos una columna esté seleccionada
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (!anyChecked) {
            alertaDiv.innerHTML = '<div class="inline-alert alert-err"><i class="bi bi-exclamation-triangle-fill"></i> Debes seleccionar al menos una columna para exportar.</div>';
            return false;
        }
        alertaDiv.innerHTML = '';
        return true;
    }

    btnExcel.addEventListener('click', function() {
        if (!validateDates()) return;
        
        frmExcel.action = "{{ route('admin.reportes.exportar') }}";
        btnExcel.classList.add('is-loading');
        btnExcel.disabled = true;
        btnPdfBulk.disabled = true;
        
        frmExcel.submit();
        
        setTimeout(() => {
            btnExcel.classList.remove('is-loading');
            btnExcel.disabled = false;
            btnPdfBulk.disabled = false;
            alertaDiv.innerHTML = '<div class="inline-alert alert-ok"><i class="bi bi-check-circle-fill"></i> El archivo Excel se está descargando.</div>';
        }, 3500);
    });

    btnPdfBulk.addEventListener('click', function() {
        if (!validateDates()) return;
        
        frmExcel.action = "{{ route('admin.reportes.exportar_pdf') }}";
        btnPdfBulk.classList.add('is-loading');
        btnExcel.disabled = true;
        btnPdfBulk.disabled = true;
        
        frmExcel.submit();
        
        setTimeout(() => {
            btnPdfBulk.classList.remove('is-loading');
            btnExcel.disabled = false;
            btnPdfBulk.disabled = false;
            alertaDiv.innerHTML = '<div class="inline-alert alert-ok"><i class="bi bi-check-circle-fill"></i> El reporte PDF se está descargando.</div>';
        }, 3500);
    });

    // ── Búsqueda AJAX con debounce 350ms ──────────────────────
    const buscarUrl = '{{ route("admin.reportes.buscar") }}';
    const resDiv    = document.getElementById('resultadosBusqueda');
    let timer       = null;

    document.getElementById('inputBusqueda').addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) {
            resDiv.innerHTML = '<div class="estado"><i class="bi bi-person-lines-fill"></i><span>Escribe al menos 2 caracteres para buscar.</span></div>';
            return;
        }
        resDiv.innerHTML = '<div class="estado"><div class="spinner-border text-primary" style="width:1.6rem;height:1.6rem;" role="status"></div><p class="mt-2 mb-0 small">Buscando…</p></div>';
        timer = setTimeout(() => buscar(q), 350);
    });

    async function buscar(q) {
        try {
            const res  = await fetch(`${buscarUrl}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error();
            const data = await res.json();

            if (!data.length) {
                resDiv.innerHTML = `<div class="estado"><i class="bi bi-person-x-fill"></i><span>Sin resultados con firma para "<strong>${esc(q)}</strong>".</span></div>`;
                return;
            }

            resDiv.innerHTML = `<p class="text-muted mt-3 mb-2" style="font-size:.8rem;"><i class="bi bi-check-circle-fill text-success me-1"></i>${data.length} resultado(s).</p>` +
                data.map(r => `
                <div class="result-card">
                    <div style="flex:1;min-width:0;">
                        <div class="rc-name">${esc(r.nombre_completo)}</div>
                        <div class="rc-meta">
                            <i class="bi bi-person-badge me-1"></i>V-${esc(r.cedula)}
                            <span class="mx-2">·</span><i class="bi bi-telephone me-1"></i>${esc(r.telefono)}
                            <span class="mx-2">·</span><i class="bi bi-pen-fill me-1"></i>${esc(r.fecha_firma)}
                            <span class="mx-2">·</span><i class="bi bi-people me-1"></i>${r.num_participantes} menor(es)
                        </div>
                    </div>
                    <a href="${esc(r.pdf_url)}" target="_blank" class="btn-pdf">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Ver PDF
                    </a>
                </div>`).join('');

        } catch {
            resDiv.innerHTML = '<div class="estado"><i class="bi bi-exclamation-triangle-fill"></i><span>Error al buscar. Intenta de nuevo.</span></div>';
        }
    }

    function esc(v) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(String(v ?? '')));
        return d.innerHTML;
    }
});
</script>
@endpush
