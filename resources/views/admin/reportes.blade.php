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
</style>
@endpush

@section('content')

{{-- ── SECCIÓN 1: EXCEL ─────────────────────────────────────── --}}
<div class="card-modern mb-4">
    <p class="section-label"><i class="bi bi-table me-1"></i>Exportación Masiva de Datos</p>
    <h5 class="font-title mb-1" style="font-size:1.05rem;">Descargar Registros en Excel (.xlsx)</h5>
    <p class="text-muted mb-4" style="font-size:.83rem;max-width:560px;">
        Genera un archivo con representantes, menores a cargo y sello exacto de firma, filtrado por rango de fechas.
    </p>

    <form id="frmExcel" action="{{ route('admin.reportes.exportar') }}" method="GET" target="_blank">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-5 col-xl-4">
                <div class="date-field">
                    <label for="fpDesde">Desde</label>
                    <i class="bi bi-calendar-event ico"></i>
                    <input type="text" id="fpDesde" name="desde" placeholder="Selecciona fecha" autocomplete="off" required>
                </div>
            </div>
            <div class="col-12 col-sm-5 col-xl-4">
                <div class="date-field">
                    <label for="fpHasta">Hasta</label>
                    <i class="bi bi-calendar-event ico"></i>
                    <input type="text" id="fpHasta" name="hasta" placeholder="Selecciona fecha" autocomplete="off" required>
                </div>
            </div>
            <div class="col-12 col-sm-2 col-xl-4">
                <button type="submit" class="btn-primary-ninja" id="btnExcel">
                    <span class="btn-spinner spin-hide"></span>
                    <i class="bi bi-file-earmark-excel-fill spin-show"></i>
                    <span class="spin-show">Descargar Excel</span>
                    <span class="spin-hide">Generando…</span>
                </button>
            </div>
        </div>
        <div id="alertaExcel"></div>
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

    // ── Spinner Excel ─────────────────────────────────────────
    const frmExcel   = document.getElementById('frmExcel');
    const btnExcel   = document.getElementById('btnExcel');
    const alertaDiv  = document.getElementById('alertaExcel');

    frmExcel.addEventListener('submit', function(e) {
        if (!document.getElementById('fpDesde').value || !document.getElementById('fpHasta').value) {
            e.preventDefault();
            alertaDiv.innerHTML = '<div class="inline-alert alert-err"><i class="bi bi-exclamation-triangle-fill"></i> Selecciona ambas fechas antes de descargar.</div>';
            return;
        }
        btnExcel.classList.add('is-loading');
        btnExcel.disabled = true;
        alertaDiv.innerHTML = '';
        setTimeout(() => {
            btnExcel.classList.remove('is-loading');
            btnExcel.disabled = false;
            alertaDiv.innerHTML = '<div class="inline-alert alert-ok"><i class="bi bi-check-circle-fill"></i> El archivo Excel se está descargando.</div>';
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
