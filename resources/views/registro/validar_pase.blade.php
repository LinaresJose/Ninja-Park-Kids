@extends('diseños.app')

@section('title', 'Validación de Pase | Ninja Park')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        @if(!$acuerdo)
        {{-- TOKEN INVÁLIDO --}}
        <div class="glass-card text-center p-5 mt-0">
            <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #dc3545, #ff6b6b); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 8px 30px rgba(220,53,69,0.4);">
                <i class="bi bi-x-lg" style="font-size: 3rem; color: white;"></i>
            </div>
            <h2 class="fw-black mb-2" style="color: #dc3545; font-size: 2rem;">PASE INVÁLIDO</h2>
            <p class="text-muted fs-5">El código QR escaneado no corresponde a ningún registro en el sistema.</p>
            <p class="text-muted">Por favor, solicite al representante que presente su pase nuevamente o contacte al administrador.</p>
        </div>

        @elseif(!$vigente)
        {{-- PASE VENCIDO --}}
        <div class="glass-card text-center p-5 mt-0">
            <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #ffa726, #ff7043); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 8px 30px rgba(255,167,38,0.4);">
                <i class="bi bi-clock-history" style="font-size: 3rem; color: white;"></i>
            </div>
            <h2 class="fw-black mb-2" style="color: #ff7043; font-size: 2rem;">PASE VENCIDO</h2>
            <p class="text-muted fs-5 mb-4">Este pase es de una visita anterior. El representante debe firmar un nuevo acuerdo para el día de hoy.</p>
            
            <div class="p-3 mb-4 text-start" style="background: rgba(255,167,38,0.08); border-radius: 12px; border-left: 4px solid #ffa726;">
                <p class="mb-1"><strong>Representante:</strong> {{ strtoupper($acuerdo->representante->nombre) }} {{ strtoupper($acuerdo->representante->apellido) }}</p>
                <p class="mb-1"><strong>Cédula:</strong> {{ $acuerdo->representante->cedula }}</p>
                <p class="mb-0"><strong>Fecha del Pase:</strong> {{ \Carbon\Carbon::parse($acuerdo->fecha_firma)->isoFormat('D [de] MMMM [de] YYYY') }}</p>
            </div>
            
            <a href="{{ route('registro.verificar') }}" class="btn-ninja" style="background: linear-gradient(135deg, #ffa726, #ff7043);">
                <i class="bi bi-arrow-repeat me-2"></i> IR A NUEVO REGISTRO
            </a>
        </div>

        @else
        {{-- PASE VIGENTE HOY ✅ --}}
        <div class="glass-card p-4 p-md-5 mt-0">
            <!-- Encabezado de Estado -->
            <div class="text-center mb-5">
                <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #20c997, #0dcaf0); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 8px 30px rgba(32,201,151,0.4);">
                    <i class="bi bi-check-lg" style="font-size: 3rem; color: white;"></i>
                </div>
                <span class="badge mb-3" style="background: linear-gradient(135deg, #20c997, #0dcaf0); font-size: 1rem; padding: 0.6em 1.2em; border-radius: 50px;">
                    ✅ PASE VIGENTE HOY
                </span>
                <h2 class="fw-black" style="color: var(--ninja-dark); font-weight: 900;">
                    {{ strtoupper($acuerdo->representante->nombre) }} {{ strtoupper($acuerdo->representante->apellido) }}
                </h2>
                <p class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ \Carbon\Carbon::parse($acuerdo->fecha_firma)->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                    &nbsp;·&nbsp;
                    <i class="bi bi-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($acuerdo->fecha_firma)->format('h:i A') }}
                </p>
            </div>

            <!-- Info del Representante -->
            <h4 class="section-title mb-3">Datos del Representante</h4>
            <div class="row g-3 mb-5">
                <div class="col-md-6">
                    <div class="p-3" style="background: rgba(255,255,255,0.7); border-radius: 10px;">
                        <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">CÉDULA</small>
                        <strong style="font-size: 1.1rem; color: var(--ninja-dark);">{{ $acuerdo->representante->cedula }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3" style="background: rgba(255,255,255,0.7); border-radius: 10px;">
                        <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">PARENTESCO</small>
                        <strong style="font-size: 1.1rem; color: var(--ninja-dark);">{{ $acuerdo->representante->parentesco }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3" style="background: rgba(255,255,255,0.7); border-radius: 10px;">
                        <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">TELÉFONO</small>
                        <strong style="font-size: 1.1rem; color: var(--ninja-dark);">{{ $acuerdo->representante->telefono }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3" style="background: rgba(255,255,255,0.7); border-radius: 10px;">
                        <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">CORREO</small>
                        <strong style="font-size: 1.1rem; color: var(--ninja-dark);">{{ $acuerdo->representante->correo }}</strong>
                    </div>
                </div>
            </div>

            <!-- Niños Autorizados -->
            <h4 class="section-title mb-3">Niños Autorizados</h4>
            <div class="row g-3">
                @foreach($acuerdo->participantes as $niño)
                <div class="col-md-6">
                    <div class="p-3 d-flex align-items-center" style="background: rgba(255,255,255,0.85); border-left: 4px solid var(--ninja-cyan); border-radius: 10px;">
                        <i class="bi bi-person-fill-check me-3" style="font-size: 1.6rem; color: var(--ninja-cyan);"></i>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: var(--ninja-dark);">{{ mb_strtoupper($niño->nombre) }} {{ mb_strtoupper($niño->apellido) }}</h6>
                            <small class="text-muted"><i class="bi bi-cake2 me-1"></i>{{ \Carbon\Carbon::parse($niño->fecha_nacimiento)->age }} años</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <p class="text-muted" style="font-size: 0.8rem;">
                    <i class="bi bi-shield-lock me-1"></i>
                    Verificado por Ninja Park &middot; Rol: <strong>Operador Integral</strong>
                </p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
