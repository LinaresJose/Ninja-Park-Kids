@extends('diseños.app')

@section('title', 'Firma de Acuerdo | Ninja Park')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        @if(session('info'))
            <div class="alert alert-info shadow-sm mb-4" style="border-radius: 12px; border-left: 5px solid var(--ninja-cyan); background: rgba(255,255,255,0.9);">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success shadow-sm mb-4" style="border-radius: 12px; border-left: 5px solid var(--ninja-neon); background: rgba(255,255,255,0.9);">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="glass-card p-4 p-md-5 mb-5 mt-0">
            <h3 class="fw-black mb-4" style="color: var(--ninja-dark); font-weight: 900;">¡HOLA DE NUEVO, {{ strtoupper($representante->nombre) }}!</h3>
            <p class="text-muted mb-5">Por favor, confirma qué niños ingresarán hoy y firma el acuerdo de responsabilidad.</p>
            
            <form action="{{ route('registro.guardarFirma', $representante->id) }}" method="POST">
                @csrf
                
                <h4 class="section-title mb-4">Información del Representante</h4>
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control bg-light" value="{{ $representante->nombre }} {{ $representante->apellido }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cédula / Pasaporte</label>
                        <input type="text" class="form-control bg-light" value="{{ $representante->cedula }}" readonly>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
                    <h4 class="section-title mb-0">Selección de Participantes</h4>
                    <button type="button" class="btn-ninja-outline" onclick="agregarNiño()">
                        <i class="bi bi-person-plus-fill me-1"></i> Añadir Nuevo Niño
                    </button>
                </div>

                <div class="mb-5">
                    <p class="small text-muted mb-3"><i class="bi bi-check2-square me-1"></i> <strong>Selecciona la casilla</strong> de los niños que ingresarán hoy al parque.</p>
                    <div class="row g-3">
                        @forelse($representante->participantes as $niño)
                        <div class="col-md-6">
                            <label class="child-card d-block position-relative" style="cursor: pointer; border-left-color: var(--ninja-purple);">
                                <div class="form-check d-flex align-items-center">
                                    {{-- Si hay errores, respetamos 'old'. Si es primera carga, marcamos todos (checked) --}}
                                    @php
                                        $isChecked = (old() && is_array(old('participantes_existentes'))) 
                                            ? in_array($niño->id, old('participantes_existentes')) 
                                            : (!old() || !count($errors)); 
                                    @endphp
                                    <input class="form-check-input" type="checkbox" name="participantes_existentes[]" value="{{ $niño->id }}" 
                                           style="transform: scale(1.5); margin-right: 15px; cursor: pointer;" 
                                           {{ $isChecked ? 'checked' : '' }}>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="color: var(--ninja-dark);">{{ mb_strtoupper($niño->nombre) }} {{ mb_strtoupper($niño->apellido) }}</h6>
                                        <small class="text-muted">Nacimiento: {{ \Carbon\Carbon::parse($niño->fecha_nacimiento)->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted">No tienes niños registrados previamente. Añade uno haciendo clic en el botón superior.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Contenedor para niños nuevos agregados en el momento -->
                <div id="contenedor-niños">
                    @if(old('nombres_niños'))
                        @foreach(old('nombres_niños') as $index => $nombre)
                        <div class="child-card mb-4 position-relative" style="border-left-color: var(--ninja-cyan);">
                            <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                <button type="button" class="btn btn-outline-danger border-0 rounded-circle" onclick="eliminarNiño(this)" title="Cancelar este niño" style="width: 40px; height: 40px;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <p class="small text-muted mb-2 fw-bold text-uppercase"><i class="bi bi-star-fill text-warning me-1"></i> Nuevo Ingreso (Recuperado)</p>
                            <div class="row g-3 align-items-end pe-5">
                                <div class="col-md-4">
                                    <label class="form-label" style="color: var(--ninja-cyan);">Nombre</label>
                                    <input type="text" name="nombres_niños[]" class="form-control" required placeholder="Ej: Maria" 
                                           minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                                           oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');"
                                           value="{{ $nombre }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="color: var(--ninja-cyan);">Apellido</label>
                                    <input type="text" name="apellidos_niños[]" class="form-control" required placeholder="Ej: Perez" 
                                           minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                                           oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');"
                                           value="{{ old('apellidos_niños')[$index] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="color: var(--ninja-cyan);">Fecha de Nacimiento</label>
                                    <input type="text" name="fechas_nacimiento_niños[]" class="form-control datepicker-child" required 
                                           placeholder="Seleccione fecha..." value="{{ old('fechas_nacimiento_niños')[$index] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <hr class="my-5" style="border-color: rgba(0,0,0,0.1);">

                <h4 class="section-title mb-4">Acuerdo de Responsabilidad</h4>
                <div class="p-4 mb-4" style="background: rgba(255,255,255,0.7); border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); max-height: 200px; overflow-y: auto; font-size: 0.9rem; color: #444; line-height: 1.6;">
                    @if(isset($termino) && $termino)
                        {!! $termino->contenido !!}
                    @else
                        <strong class="text-danger">Aviso:</strong> No hay términos de responsabilidad activos cargados en la base de datos.
                    @endif
                </div>

                <div class="form-check mb-5 d-flex align-items-center p-4" style="background: rgba(230, 0, 126, 0.05); border-radius: 12px; border: 1px solid rgba(230, 0, 126, 0.2);">
                    <input class="form-check-input me-3 ms-1" type="checkbox" name="aceptar_terminos" id="aceptarTerminos" required style="transform: scale(1.8); cursor: pointer;">
                    <label class="form-check-label fw-bold" for="aceptarTerminos" style="font-size: 1.1rem; color: var(--ninja-pink); cursor: pointer;">
                        He leído y acepto los términos y condiciones de responsabilidad civil.
                    </label>
                </div>

                <h4 class="section-title mb-4">Firma Electrónica</h4>
                <div class="mb-5">
                    <label class="form-label text-muted d-block mb-3"><i class="bi bi-pen me-1"></i> Por favor, firme en el recuadro inferior:</label>
                    <div style="border: 2px dashed var(--ninja-purple); border-radius: 12px; background: #fff; position: relative;">
                        <!-- Canvas -->
                        <canvas id="signature-pad" class="signature-pad" style="touch-action: none; border-radius: 12px; width: 100%; height: 200px; cursor: crosshair;"></canvas>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-signature">
                            <i class="bi bi-eraser-fill"></i> Limpiar Firma
                        </button>
                    </div>
                    <!-- Campo oculto donde se guardará la imagen en base64 -->
                    <input type="hidden" name="firma_base64" id="firma_base64">
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn-ninja py-3 shadow-lg" style="font-size: 1.2rem;">
                        <i class="bi bi-pen-fill me-2"></i> FIRMAR ACUERDO Y GENERAR PASE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function agregarNiño() {
        const contenedorNuevos = document.getElementById('contenedor-niños');
        const totalExistentes = {{ $representante->participantes->count() }};
        const totalNuevos = contenedorNuevos.querySelectorAll('.child-card').length;

        if ((totalExistentes + totalNuevos) >= 15) {
            alert('Has alcanzado el límite máximo de 15 niños registrados para este representante.');
            return;
        }

        const div = document.createElement('div');
        div.className = 'child-card mb-4 position-relative';
        div.style.opacity = '0';
        div.style.transform = 'translateY(20px)';
        div.style.transition = 'all 0.4s ease';
        div.style.borderLeftColor = 'var(--ninja-cyan)';
        
        div.innerHTML = `
            <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                <button type="button" class="btn btn-outline-danger border-0 rounded-circle" onclick="eliminarNiño(this)" title="Cancelar este niño" style="width: 40px; height: 40px;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <p class="small text-muted mb-2 fw-bold text-uppercase"><i class="bi bi-star-fill text-warning me-1"></i> Nuevo Ingreso</p>
            <div class="row g-3 align-items-end pe-5">
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--ninja-cyan);">Nombre</label>
                    <input type="text" name="nombres_niños[]" class="form-control" required placeholder="Ej: Maria" 
                           minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                           oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--ninja-cyan);">Apellido</label>
                    <input type="text" name="apellidos_niños[]" class="form-control" required placeholder="Ej: Perez" 
                           minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                           oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--ninja-cyan);">Fecha de Nacimiento</label>
                    <input type="text" name="fechas_nacimiento_niños[]" class="form-control datepicker-child" required 
                           placeholder="Seleccione fecha...">
                </div>
            </div>`;
            
        document.getElementById('contenedor-niños').appendChild(div);

        // Inicializar Flatpickr en el nuevo campo
        flatpickr(div.querySelector('.datepicker-child'), {
            locale: "es",
            dateFormat: "Y-m-d",
            maxDate: "today",
            disableMobile: "true"
        });
        
        // Trigger reflow
        void div.offsetWidth;
        div.style.opacity = '1';
        div.style.transform = 'translateY(0)';
    }

    function eliminarNiño(btn) {
        const card = btn.closest('.child-card');
        card.style.opacity = '0';
        card.style.transform = 'translateX(20px)';
        setTimeout(() => {
            card.remove();
        }, 400);
    }

    // Inicialización Global
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".datepicker-child", {
            locale: "es",
            dateFormat: "Y-m-d",
            maxDate: "today",
            disableMobile: "true",
            placeholder: "Fecha de nacimiento del niño"
        });

        // Configuración de Signature Pad
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 1)', 
            penColor: 'rgb(0, 0, 0)'
        });

        // Redimensionamiento Responsivo (Soporte Retina)
        function resizeCanvas() {
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Limpia al redimensionar porque el contexto cambia
        }

        // Ejecutar al inicio y al cambiar el tamaño de ventana
        window.onresize = resizeCanvas;
        resizeCanvas();

        // Botón limpiar
        document.getElementById('clear-signature').addEventListener('click', function () {
            signaturePad.clear();
        });

        // Interceptar el envío del formulario
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Por favor, dibuje su firma en el recuadro antes de continuar.');
            } else {
                // Generar Base64 y guardarla en el input hidden
                document.getElementById('firma_base64').value = signaturePad.toDataURL();
            }
        });
    });
</script>
<script src="{{ asset('js/signature_pad.min.js') }}"></script>
@endpush
