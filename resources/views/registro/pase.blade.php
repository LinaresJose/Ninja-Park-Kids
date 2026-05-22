@extends('diseños.app')

@section('title', 'Pase Exitoso | Ninja Park')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        
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

        <div class="glass-card text-center p-5 mb-5 mt-0">
            <!-- Icono Grande de Éxito -->
            <div class="mb-2" style="position: relative; display: inline-block;">
                <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #20c997, #0dcaf0); display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 8px 30px rgba(32,201,151,0.4);">
                    <i class="bi bi-check-lg" style="font-size: 3rem; color: white;"></i>
                </div>
            </div>
            
            <h2 class="fw-black mt-4 mb-1" style="color: var(--ninja-dark); font-weight: 900; font-size: 2.2rem;">¡REGISTRO VIGENTE!</h2>
            <p class="text-muted mb-1 fs-5">
                <strong>{{ strtoupper($acuerdo->representante->nombre) }} {{ strtoupper($acuerdo->representante->apellido) }}</strong>
            </p>
            <p class="text-muted mb-5">
                <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($acuerdo->fecha_firma)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
            </p>

            <!-- SECCIÓN DEL CÓDIGO QR -->
            <div class="row justify-content-center mb-4">
                <div class="col-auto">
                    <div class="p-4" style="background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.12); border: 2px solid rgba(123, 44, 191, 0.15); display: inline-block;">
                        <!-- Imagen del QR generada por el servidor -->
                        <img id="qr-image"
                             src="{{ route('registro.qr', $acuerdo->id) }}"
                             alt="Código QR del Pase"
                             width="260"
                             height="260"
                             style="display: block; border-radius: 8px;">
                    </div>
                    <p class="text-muted mt-3" style="font-size: 0.82rem;">
                        <i class="bi bi-qr-code me-1"></i> Escanee este código en taquilla para verificar el pase.
                    </p>

                    {{-- Botón SOLO visible en móviles (controlado por JS + CSS) --}}
                    <div id="download-btn-container" style="display: none;" class="mt-2">
                        <a id="download-qr-btn"
                           href="{{ route('registro.qr', $acuerdo->id) }}"
                           download="QR_{{ strtoupper($acuerdo->representante->nombre) }}_{{ strtoupper($acuerdo->representante->apellido) }}.svg"
                           class="btn btn-outline-success fw-bold">
                            <i class="bi bi-download me-2"></i> Descargar Mi QR
                        </a>
                    </div>
                </div>
            </div>

            <!-- NIÑOS AUTORIZADOS HOY -->
            <h4 class="section-title mb-4 text-start">Niños Autorizados Hoy</h4>
            <div class="row g-3 text-start mb-5">
                @forelse($acuerdo->participantes as $niño)
                <div class="col-md-6">
                    <div class="p-3 d-flex align-items-center" style="background: rgba(255,255,255,0.85); border-left: 4px solid var(--ninja-cyan); border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
                        <i class="bi bi-person-fill-check me-3" style="font-size: 1.6rem; color: var(--ninja-cyan);"></i>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: var(--ninja-dark);">{{ mb_strtoupper($niño->nombre) }} {{ mb_strtoupper($niño->apellido) }}</h6>
                            <small class="text-muted"><i class="bi bi-cake2 me-1"></i>{{ \Carbon\Carbon::parse($niño->fecha_nacimiento)->age }} años</small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted">No hay niños registrados en este pase.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-2">
                <a href="{{ route('registro.verificar') }}" class="btn-ninja">
                    <i class="bi bi-house-door-fill me-2"></i> VOLVER AL INICIO
                </a>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar botón de descarga SOLO en dispositivos móviles (User-Agent + media query)
    (function() {
        const isMobile = /Mobi|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
                         || window.matchMedia('(max-width: 767px)').matches;
        if (isMobile) {
            document.getElementById('download-btn-container').style.display = 'block';
        }
    })();
</script>
@endpush
