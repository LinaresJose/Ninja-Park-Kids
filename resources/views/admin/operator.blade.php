@extends('diseños.app')

@section('title', 'Panel Operador | Ninja Park')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        
        <!-- Header del Operador -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-black mb-0" style="color: var(--ninja-dark); font-weight: 800;">HOLA, {{ strtoupper(Auth::user()->nombre) }}</h2>
                <p class="text-muted"><i class="bi bi-person-badge me-1"></i> Rol: Operador Integral</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                    <i class="bi bi-box-arrow-right me-1"></i> SALIR
                </button>
            </form>
        </div>

        <!-- Botones de Acción Gigantes para Tablet -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-6">
                <button class="btn w-100 p-5 shadow-lg d-flex flex-column align-items-center justify-content-center" 
                        style="background: linear-gradient(135deg, var(--ninja-purple), var(--ninja-pink)); border-radius: 24px; border: none; min-height: 200px;">
                    <i class="bi bi-qr-code-scan mb-3" style="font-size: 4rem; color: white;"></i>
                    <span class="fw-bold text-white fs-4">ABRIR ESCÁNER</span>
                </button>
            </div>
            <div class="col-12 col-md-6">
                <a href="{{ route('registro.verificar') }}" class="btn w-100 p-5 shadow-lg d-flex flex-column align-items-center justify-content-center" 
                        style="background: white; border-radius: 24px; border: 2px solid var(--ninja-purple); min-height: 200px; text-decoration: none;">
                    <i class="bi bi-person-plus-fill mb-3" style="font-size: 4rem; color: var(--ninja-purple);"></i>
                    <span class="fw-bold fs-4" style="color: var(--ninja-purple);">NUEVO REGISTRO</span>
                </a>
            </div>
        </div>

        <!-- Lista de ingresos de hoy -->
        <h4 class="section-title mb-4">Ingresos de Hoy ({{ count($firmasHoy) }})</h4>
        <div class="row g-3 text-start">
            @forelse($firmasHoy as $acuerdo)
            <div class="col-12">
                <div class="glass-card p-3 d-flex justify-content-between align-items-center" style="border-left: 5px solid var(--ninja-neon); border-radius: 16px;">
                    <div>
                        <h6 class="mb-1 fw-bold" style="color: var(--ninja-dark);">
                            {{ strtoupper($acuerdo->representante->nombre) }} {{ strtoupper($acuerdo->representante->apellido) }}
                        </h6>
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-telephone-fill me-1"></i> {{ $acuerdo->representante->telefono }} &middot; 
                            <i class="bi bi-people-fill me-1"></i> {{ $acuerdo->participantes->count() }} niño(s)
                        </p>
                        <div class="mt-2">
                            @foreach($acuerdo->participantes as $niño)
                                <span class="badge bg-light text-dark me-1 border" style="font-size: 0.7rem;">
                                    <i class="bi bi-person-check me-1"></i> {{ mb_strtoupper($niño->nombre) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block mb-1">{{ \Carbon\Carbon::parse($acuerdo->fecha_firma)->format('h:i A') }}</small>
                        <span class="badge" style="background: var(--ninja-neon); color: var(--ninja-dark);">ACEPTADO</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center p-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">No se han registrado visitas el día de hoy.</p>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
