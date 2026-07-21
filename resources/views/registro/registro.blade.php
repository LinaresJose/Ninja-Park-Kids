@extends('diseños.app')

@section('title', 'Registro de Visitantes | Ninja Park')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="glass-card p-4 p-md-5 mb-5 mt-0">
            <form action="{{ route('registro.store') }}" method="POST">
                @csrf
                
                <h4 class="section-title">Información del Representante</h4>
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <label class="form-label">Cédula / Pasaporte</label>
                        <input type="text" name="cedula" class="form-control" value="{{ old('cedula', $cedula ?? '') }}" readonly required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan" required 
                               minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');"
                               value="{{ old('nombre') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" placeholder="Ej: Pérez" required 
                               minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');"
                               value="{{ old('apellido') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Parentesco</label>
                        <select name="parentesco" class="form-select" required>
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="Padre" {{ old('parentesco') == 'Padre' ? 'selected' : '' }}>Padre</option>
                            <option value="Madre" {{ old('parentesco') == 'Madre' ? 'selected' : '' }}>Madre</option>
                            <option value="Representante Legal" {{ old('parentesco') == 'Representante Legal' ? 'selected' : '' }}>Representante Legal</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" placeholder="juan@correo.com" required
                               value="{{ old('correo') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" name="telefono" class="form-control" placeholder="Ej: 04141234567" required
                               inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="11"
                               value="{{ old('telefono') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="text" name="fecha_nacimiento" class="form-control datepicker-rep" required 
                               placeholder="Seleccione fecha..." value="{{ old('fecha_nacimiento') }}">
                        <small class="text-muted" style="font-size: 0.7rem;">Debe ser mayor de 18 años.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="section-title mb-0">Niños (Participantes)</h4>
                    <button type="button" class="btn-ninja-outline" onclick="agregarNiño()">
                        <i class="bi bi-person-plus-fill me-1"></i> Añadir Niño
                    </button>
                </div>

                <div id="contenedor-niños">
                    @php
                        $oldNombres = old('nombres_niños');
                        $oldApellidos = old('apellidos_niños');
                        $oldFechas = old('fechas_nacimiento_niños');
                    @endphp

                    @if($oldNombres && is_array($oldNombres))
                        @foreach($oldNombres as $index => $nombre)
                        <div class="child-card mb-4 position-relative">
                            @if($index > 0)
                            <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                <button type="button" class="btn btn-outline-danger border-0 rounded-circle" onclick="eliminarNiño(this)" title="Eliminar este niño" style="width: 40px; height: 40px;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            @endif
                            <div class="row g-3 align-items-end {{ $index > 0 ? 'pe-5' : 'pe-4' }}">
                                <div class="col-md-4">
                                    <label class="form-label" style="color: var(--ninja-purple);">Nombre del Niño</label>
                                    <input type="text" name="nombres_niños[]" class="form-control" required 
                                           minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                                           oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');"
                                           value="{{ $nombre }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="color: var(--ninja-purple);">Apellido del Niño</label>
                                    <input type="text" name="apellidos_niños[]" class="form-control" required 
                                           minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                                           oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');"
                                           value="{{ $oldApellidos[$index] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" style="color: var(--ninja-purple);">Fecha de Nacimiento</label>
                                    <input type="text" name="fechas_nacimiento_niños[]" class="form-control datepicker-child" required 
                                           placeholder="Seleccione fecha..." value="{{ $oldFechas[$index] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="child-card mb-4 position-relative">
                        <div class="row g-3 align-items-end pe-4">
                            <div class="col-md-4">
                                <label class="form-label" style="color: var(--ninja-purple);">Nombre del Niño</label>
                                <input type="text" name="nombres_niños[]" class="form-control" required 
                                       minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                                       oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="color: var(--ninja-purple);">Apellido del Niño</label>
                                <input type="text" name="apellidos_niños[]" class="form-control" required 
                                       minlength="2" maxlength="50" pattern="^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$"
                                       oninput="this.value = this.value.replace(/[^a-zA-Z\sñÑáéíóúÁÉÍÓÚ]/g, '');">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="color: var(--ninja-purple);">Fecha de Nacimiento</label>
                                <input type="text" name="fechas_nacimiento_niños[]" class="form-control datepicker-child" required 
                                       placeholder="Seleccione fecha...">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <h4 class="section-title mb-4">Acuerdo de Responsabilidad</h4>
                <div class="p-4 mb-4" style="background: rgba(255,255,255,0.7); border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); max-height: 200px; overflow-y: auto; font-size: 0.9rem; color: #444; line-height: 1.6;">
                    @if(isset($termino) && $termino)
                        {!! $termino->contenido !!}
                    @else
                        <strong class="text-danger">Aviso:</strong> No hay términos de responsabilidad activos cargados en la base de datos.
                    @endif
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

                <div class="d-grid mt-5">
                    <button type="submit" class="btn-ninja py-3 shadow-lg" style="font-size: 1.1rem;">
                        <i class="bi bi-qr-code-scan me-2"></i> ACEPTAR TÉRMINOS Y GENERAR PASE
                    </button>
                    <p class="text-center mt-3 text-muted" style="font-weight: 600; font-size: 0.9rem;">
                        Al hacer clic, usted confirma que es mayor de edad y responsable de los menores registrados.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function agregarNiño() {
        const contenedor = document.getElementById('contenedor-niños');
        const cantidadActual = contenedor.querySelectorAll('.child-card').length;

        if (cantidadActual >= 15) {
            alert('Has alcanzado el límite máximo de 15 niños por representante.');
            return;
        }

        const div = document.createElement('div');
        div.className = 'child-card mb-4 position-relative';
        div.style.opacity = '0';
        div.style.transform = 'translateY(20px)';
        div.style.transition = 'all 0.4s ease';
        
        div.innerHTML = `
            <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                <button type="button" class="btn btn-outline-danger border-0 rounded-circle" onclick="eliminarNiño(this)" title="Eliminar este niño" style="width: 40px; height: 40px;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="row g-3 align-items-end pe-5">
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--ninja-purple);">Nombre del Niño</label>
                    <input type="text" name="nombres_niños[]" class="form-control" required 
                           minlength="2" maxlength="50" pattern="^[a-zA-Z\\sñÑáéíóúÁÉÍÓÚ]+$"
                           oninput="this.value = this.value.replace(/[^a-zA-Z\\sñÑáéíóúÁÉÍÓÚ]/g, '');">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--ninja-purple);">Apellido del Niño</label>
                    <input type="text" name="apellidos_niños[]" class="form-control" required 
                           minlength="2" maxlength="50" pattern="^[a-zA-Z\\sñÑáéíóúÁÉÍÓÚ]+$"
                           oninput="this.value = this.value.replace(/[^a-zA-Z\\sñÑáéíóúÁÉÍÓÚ]/g, '');">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--ninja-purple);">Fecha de Nacimiento</label>
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
        
        // Trigger reflow immediately to ensure the CSS transition triggers
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

    // Inicialización Global de Flatpickr
    document.addEventListener('DOMContentLoaded', function() {
        // Para el representante (18+ años)
        flatpickr(".datepicker-rep", {
            locale: "es",
            dateFormat: "Y-m-d",
            maxDate: "{{ date('Y-m-d', strtotime('-18 years')) }}",
            disableMobile: "true",
            placeholder: "Seleccione su fecha de nacimiento"
        });

        // Para los niños existentes
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

        // Interceptar el envío del formulario con prevención de doble submit
        const form = document.querySelector('form');
        let formEnviado = false;

        form.addEventListener('submit', function(e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Por favor, dibuje su firma en el recuadro antes de continuar.');
                return;
            }

            if (formEnviado) {
                e.preventDefault();
                return;
            }

            formEnviado = true;
            document.getElementById('firma_base64').value = signaturePad.toDataURL();

            // Deshabilitar botón de envío para evitar peticiones duplicadas
            const btnSubmit = form.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> PROCESANDO PASE...';
            }
        });
    });
</script>
<script src="{{ asset('js/signature_pad.min.js') }}"></script>
@endpush