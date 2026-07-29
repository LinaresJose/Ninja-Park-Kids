@extends('diseños.staff')

@section('title', 'Control de Acceso | Ninja Park')
@section('title_header', 'Control de Escáner y Accesos')

@section('content')
<div id="operadorPanelEl" x-data="operadorPanel()" x-init="init()" data-registros="{{ json_encode($registros->map(function($acc) {
    return [
        'id' => $acc->id,
        'token' => $acc->token_qr,
        'fecha' => \Carbon\Carbon::parse($acc->fecha_firma)->format('d/m/Y H:i'),
        'representante' => $acc->representante->nombre . ' ' . $acc->representante->apellido,
        'cedula' => $acc->representante->cedula,
        'telefono' => $acc->representante->telefono,
        'niños' => $acc->participantes->pluck('nombre')->toArray(),
        'status' => 'Vigente'
    ];
})->toArray()) }}" style="position: relative; z-index: 1050;">
    
    <!-- HEADER DEL PANEL DE TRABAJO -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
        <div>
            <h4 class="font-title mb-1 fw-black">Escáner Principal</h4>
            <p class="text-muted small mb-0">Listo para escanear y validar accesos</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button @click="toggleScanner()" class="btn text-white rounded-3 shadow-sm fw-bold px-4 py-2" :style="scannerActive ? 'background: #EF4444;' : 'background: var(--primary);'">
                <i class="bi" :class="scannerActive ? 'bi-camera-video-off' : 'bi-camera-video'"></i>
                <span x-text="scannerActive ? 'CERRAR CÁMARA' : 'USAR CÁMARA'"></span>
            </button>
        </div>
    </div>

    <!-- Módulo de Escaneo de Cámara (Oculto por defecto) -->
    <div x-show="scannerActive" x-transition class="row mb-4">
        <div class="col-12">
            <div class="card-modern text-center">
                <div id="reader" style="width: 100%; max-width: 600px; margin: 0 auto; border-radius: 12px; overflow: hidden; border: 2px dashed #E2E8F0;"></div>
                <style>
                    #reader video {
                        transform: scaleX(1) !important;
                    }
                </style>
            </div>
        </div>
    </div>

    <!-- BARRA DE BÚSQUEDA Y FILTROS -->
    <div class="row mb-4 g-3">
        <div class="col-md-8">
            <div class="input-group input-group-lg" style="border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0;">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="bi bi-search" style="color: var(--primary);"></i>
                </span>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="buscar()" 
                       class="form-control border-0 bg-white" 
                       style="padding-top: 12px; padding-bottom: 12px; font-size: 16px;"
                       placeholder="Buscar Cédula, Nombre o Token..."
                       autocomplete="off">
                <button x-show="searchQuery" @click="resetBusqueda()" class="btn bg-white border-0 pe-4">
                    <i class="bi bi-x-circle-fill text-muted"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group input-group-lg" style="border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0;">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="bi bi-calendar-event" style="color: var(--primary);"></i>
                </span>
                <input type="date" x-model="filterDate" @change="buscar()" 
                       class="form-control border-0 bg-white" 
                       style="padding-top: 12px; padding-bottom: 12px; font-size: 16px;">
            </div>
        </div>
    </div>

    <!-- TABLA DE RESULTADOS -->
    <div class="card-modern p-0 overflow-hidden">
        <div class="p-4 border-bottom" style="border-color: #F1F5F9 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="font-title mb-0">Entradas del Período</h5>
                <button @click="resetBusqueda()" class="btn d-inline-flex align-items-center" style="background-color: #F1F5F9; color: #475569; border-radius: 8px; font-size: 0.85rem; font-weight: 600; padding: 6px 12px; border: none; box-shadow: none;">
                    <i class="bi bi-arrow-clockwise me-2"></i>REINICIAR
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table-modern table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">TOKEN</th>
                        <th>REPRESENTANTE / CONTACTO</th>
                        <th>PARTICIPANTES</th>
                        <th>FECHA / HORA</th>
                        <th class="text-end" style="padding-right: 24px;">ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in registros" :key="item.id">
                        <tr @click="openModal(item.token)" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td class="ps-4">
                                <code class="fw-bold" style="color: var(--primary); font-size: 0.95rem;" x-text="item.token.substring(0, 8) + '...'"></code>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" x-text="item.representante"></div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <small class="text-muted"><i class="bi bi-card-text me-1"></i><span x-text="item.cedula"></span></small>
                                    <span class="text-muted small">|</span>
                                    <small class="fw-bold text-success"><i class="bi bi-whatsapp me-1"></i><span x-text="item.telefono"></span></small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <template x-for="niño in item.niños">
                                        <span class="badge border bg-white text-dark rounded-pill shadow-sm" style="font-weight: 500;" x-text="niño"></span>
                                    </template>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark" x-text="item.fecha.split(' ')[0]"></div>
                                <small class="text-muted" x-text="item.fecha.split(' ')[1]"></small>
                            </td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-light border fw-bold" style="color: var(--primary);">VALIDAR</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="registros.length === 0">
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-ticket-detailed" style="font-size: 2.5rem; color: #9CA3AF; opacity: 0.4;"></i>
                            <p class="text-muted mt-2 fw-medium">No se encontraron registros para esta búsqueda.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Input Oculto para Escáner HID -->
    <input type="text" x-ref="hidInput" @keydown.enter="handleHidScan($event)" 
           style="position: absolute; left: -9999px; opacity: 0;" autocomplete="off">

    <!-- MODAL DE VALIDACIÓN -->
    <div x-show="modalVisible" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed-top w-100 h-100" 
         style="background: rgba(15, 23, 42, 0.85); z-index: 9999; backdrop-filter: blur(8px); display: none;">
        
        <div class="w-100 h-100 d-flex align-items-center justify-content-center">
            <div class="card-modern border-0 p-5 shadow-lg" style="max-width: 650px; width: 95%; position: relative;">
            <button @click="modalVisible = false" class="btn btn-light rounded-circle position-absolute" style="top: 15px; right: 15px; width: 40px; height: 40px;">
                <i class="bi bi-x-lg"></i>
            </button>
            
            <div class="text-center mb-4">
                <span class="badge-pastel px-4 py-2 mb-3 d-inline-block font-title" 
                      :class="validData.status === 'Vigente' ? 'badge-pastel-success' : 'badge-pastel-error'"
                      style="font-size: 1rem;" x-text="validData.status.toUpperCase() + ' - PASE AUTORIZADO'"></span>
                
                <h2 class="fw-black text-uppercase font-title" style="word-break: break-word; color: var(--text-main);" x-text="validData.representante"></h2>
                <div class="d-flex justify-content-center gap-3">
                    <p class="text-muted fw-bold mb-0" x-text="'DOC: ' + validData.cedula"></p>
                    <p class="text-success fw-bold mb-0" x-text="'TEL: ' + validData.telefono"></p>
                </div>
            </div>

            <div class="bg-light rounded-3 p-4 mb-4 border">
                <h6 class="font-title fw-bold text-muted mb-3"><i class="bi bi-people-fill me-2"></i>PARTICIPANTES HABILITADOS</h6>
                <div class="d-grid gap-2">
                    <template x-for="niño in validData.niños">
                        <div class="d-flex align-items-center bg-white p-3 rounded shadow-sm border-start border-4" style="border-color: var(--badge-success-text) !important;">
                            <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                            <div class="flex-grow-1">
                                <span class="fw-bold d-block text-dark" style="font-size: 1.1rem;" x-text="niño.nombre"></span>
                            </div>
                            <span class="badge border bg-light text-dark px-3 py-2 rounded-pill" x-text="niño.edad + ' AÑOS'"></span>
                        </div>
                    </template>
                </div>
            </div>

            <button @click="modalVisible = false" class="btn text-white w-100 py-3 fw-bold rounded-3 font-title" style="background: var(--primary); font-size: 1.1rem; box-shadow: 0 4px 15px rgba(109, 40, 217, 0.3);">
                FINALIZAR REVISIÓN
            </button>
            
            <div class="text-center mt-3">
                <small class="text-muted">TOKEN REF: <span class="font-monospace fw-bold" x-text="validData.token"></span></small>
            </div>
        </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/html5-qrcode.min.js') }}"></script>
<script>
    function operadorPanel() {
        return {
            registros: JSON.parse(document.getElementById('operadorPanelEl').dataset.registros),
            searchQuery: '',
            filterDate: '',
            scannerActive: false,
            html5QrCode: null,
            modalVisible: false,
            validData: { representante: '', niños: [], status: '', token: '', telefono: '', cedula: '' },
            
            init() {
                this.refocusScanner();
                document.addEventListener('click', (e) => {
                    // Solo refocusear si el click no es dentro de una caja de texto
                    if(!e.target.closest('input[type="text"]') && !e.target.closest('input[type="date"]')) {
                        this.refocusScanner();
                    }
                });
                
                try {
                    this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                } catch(e) {}
            },

            refocusScanner() {
                if (!this.modalVisible && !this.scannerActive) {
                    this.$refs.hidInput.focus({ preventScroll: true });
                }
            },

            buscar() {
                if (this.searchQuery.length < 2 && this.filterDate === '') {
                    // Si limpió todo, podríamos recargar pero mejor esperamos o manejamos reset
                    return;
                }
                
                const params = new URLSearchParams();
                if (this.searchQuery) params.append('q', this.searchQuery);
                if (this.filterDate) params.append('fecha', this.filterDate);

                fetch(`{{ route('operador.buscar') }}?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => this.registros = data)
                    .catch(err => console.error(err));
            },

            resetBusqueda() {
                this.searchQuery = '';
                this.filterDate = '';
                location.reload();
            },

            async openModal(token) {
                // Si el token es una URL (escaneado por cámara), extraemos solo el UUID final
                if (token.includes('/validar/')) {
                    token = token.split('/validar/').pop();
                }

                try {
                    const res = await fetch(`/staff-ninja/api/operador/validar/${token}`);
                    const data = await res.json();
                    
                    if (data.success) {
                        this.validData = data;
                        this.validData.token = token;
                        this.modalVisible = true;
                        this.playBeep(880, 0.15); // Beep de éxito
                    } else {
                        throw new Error("Pase no encontrado");
                    }
                } catch (e) {
                    this.playBeep(220, 0.4); // Buzz de error
                    Swal.fire({
                        title: 'Token Inválido',
                        text: 'El código escaneado no corresponde a ningún registro vigente en el sistema.',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        customClass: { title: 'font-title' }
                    });
                }
            },

            handleHidScan(e) {
                const token = e.target.value.trim();
                if (token) {
                    this.openModal(token);
                }
                e.target.value = '';
                this.refocusScanner();
            },

            toggleScanner() {
                this.scannerActive = !this.scannerActive;
                if (this.scannerActive) {
                    this.startCamera();
                } else {
                    this.stopCamera();
                }
            },

            startCamera() {
                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode("reader");
                } else {
                    this.html5QrCode.clear();
                }
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                this.html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                    this.stopCamera();
                    this.scannerActive = false;
                    this.openModal(decodedText);
                }).catch((err) => {
                    Swal.fire({
                        title: 'Sin Acceso a Cámara',
                        text: 'No se encontraron dispositivos o el navegador bloqueó el permiso.',
                        icon: 'warning',
                        confirmButtonColor: '#6D28D9',
                        customClass: { title: 'font-title' }
                    });
                    this.scannerActive = false;
                });
            },

            stopCamera() {
                if (this.html5QrCode) {
                    this.html5QrCode.stop().then(() => this.html5QrCode.clear()).catch(e => {});
                }
            },

            playBeep(frequency, duration) {
                if(!this.audioCtx) return;
                const oscillator = this.audioCtx.createOscillator();
                const gainNode = this.audioCtx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(this.audioCtx.destination);

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(frequency, this.audioCtx.currentTime);
                
                gainNode.gain.setValueAtTime(0.1, this.audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + duration);

                oscillator.start();
                oscillator.stop(this.audioCtx.currentTime + duration);
            }
        }
    }
</script>
@endpush
