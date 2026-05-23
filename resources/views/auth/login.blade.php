@extends('diseños.app')

@section('title', 'Acceso Interno | Ninja Park')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="glass-card p-5">
            <div class="text-center mb-5">
                <div class="mb-3" style="display: inline-block; padding: 15px; border-radius: 50%; background: var(--ninja-purple); color: white;">
                    <i class="bi bi-shield-lock-fill" style="font-size: 2.5rem;"></i>
                </div>
                <h2 class="fw-black" style="color: var(--ninja-dark); font-weight: 900;">PORTAL STAFF</h2>
                <p class="text-muted">Ninja Park Management System</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="correo" class="form-label" style="font-weight: 700; color: var(--ninja-purple);">Correo Corporativo</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: white; border-right: none; border-radius: 12px 0 0 12px;"><i class="bi bi-envelope-at text-muted"></i></span>
                        <input type="email" name="correo" id="correo" class="form-control" style="border-left: none; border-radius: 0 12px 12px 0;" placeholder="nombre@ninjapark.com" value="{{ old('correo') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="form-label" style="font-weight: 700; color: var(--ninja-purple);">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: white; border-right: none; border-radius: 12px 0 0 12px;"><i class="bi bi-key text-muted"></i></span>
                        <input type="password" name="password" id="password" class="form-control" style="border-left: none; border-radius: 0 12px 12px 0;" placeholder="••••••••" required>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4" style="border-radius: 12px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-grid mb-4">
                    <button type="submit" class="btn-ninja py-3 shadow-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i> INGRESAR AL PANEL
                    </button>
                </div>
            </form>

            <div class="text-center mb-3">
                <a href="{{ route('password.request') }}" class="text-primary text-decoration-none small fw-bold">
                    <i class="bi bi-question-circle me-1"></i> ¿Olvidaste tu contraseña?
                </a>
            </div>

            <div class="text-center">
                <a href="{{ route('registro.verificar') }}" class="text-muted text-decoration-none small">
                    <i class="bi bi-chevron-left me-1"></i> Volver al Registro de Clientes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
