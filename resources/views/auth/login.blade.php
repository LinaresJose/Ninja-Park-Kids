@extends('diseños.app')

@section('title', 'Acceso Interno | Ninja Park')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <x-ui.glass-card>
            <div class="text-center mb-5">
                <div class="mb-3 d-inline-block p-3 rounded-circle" style="background: var(--ninja-purple); color: white;">
                    <i class="bi bi-shield-lock-fill" style="font-size: 2.5rem;"></i>
                </div>
                <h2 class="fw-black" style="color: var(--ninja-dark); font-weight: 900;">PORTAL DEL PERSONAL</h2>
                <p class="text-muted">Ninja Park Management System</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <x-ui.input-group 
                    id="correo" 
                    name="correo" 
                    type="email" 
                    label="Correo Corporativo" 
                    icon="envelope-at" 
                    placeholder="nombre@ninjapark.com" 
                    value="{{ old('correo') }}" 
                    required autofocus 
                />

                <x-ui.input-group 
                    id="password" 
                    name="password" 
                    type="password" 
                    label="Contraseña" 
                    icon="key" 
                    placeholder="••••••••" 
                    required 
                />

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 rounded-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-grid mb-4">
                    <x-ui.button type="submit" icon="box-arrow-in-right" class="w-100">
                        INGRESAR AL PANEL
                    </x-ui.button>
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
        </x-ui.glass-card>
    </div>
</div>
@endsection
