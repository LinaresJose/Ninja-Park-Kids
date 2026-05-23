@extends('diseños.app')

@section('title', 'Recuperar Contraseña | Ninja Park')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="glass-card p-5">
            <div class="text-center mb-5">
                <div class="mb-3" style="display: inline-block; padding: 15px; border-radius: 50%; background: var(--ninja-purple); color: white;">
                    <i class="bi bi-key-fill" style="font-size: 2.5rem;"></i>
                </div>
                <h2 class="fw-black" style="color: var(--ninja-dark); font-weight: 900;">¿Olvidaste tu contraseña?</h2>
                <p class="text-muted">Introduce tu correo corporativo y te enviaremos un enlace para restablecer tu acceso.</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center mb-4" style="border-radius: 12px;">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4" style="border-radius: 12px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <div class="mb-5">
                    <label for="correo" class="form-label" style="font-weight: 700; color: var(--ninja-purple);">Correo Corporativo</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: white; border-right: none; border-radius: 12px 0 0 12px;"><i class="bi bi-envelope-at text-muted"></i></span>
                        <input type="email" name="correo" id="correo" class="form-control" style="border-left: none; border-radius: 0 12px 12px 0;" placeholder="nombre@ninjapark.com" value="{{ old('correo') }}" required autofocus>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn-ninja py-3 shadow-lg">
                        <i class="bi bi-send me-2"></i> ENVIAR ENLACE
                    </button>
                </div>
            </form>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-muted text-decoration-none small">
                    <i class="bi bi-chevron-left me-1"></i> Volver al Inicio de Sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
