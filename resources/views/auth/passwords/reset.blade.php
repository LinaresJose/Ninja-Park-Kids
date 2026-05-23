@extends('diseños.app')

@section('title', 'Restablecer Contraseña | Ninja Park')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="glass-card p-5">
            <div class="text-center mb-5">
                <div class="mb-3" style="display: inline-block; padding: 15px; border-radius: 50%; background: var(--ninja-purple); color: white;">
                    <i class="bi bi-shield-lock-fill" style="font-size: 2.5rem;"></i>
                </div>
                <h2 class="fw-black" style="color: var(--ninja-dark); font-weight: 900;">Restablecer Contraseña</h2>
                <p class="text-muted">Introduce tu nueva contraseña de acceso corporativo.</p>
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

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="correo" value="{{ $correo }}">

                <div class="mb-4">
                    <label class="form-label" style="font-weight: 700; color: var(--ninja-purple);">Correo Corporativo</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: #e9ecef; border-right: none; border-radius: 12px 0 0 12px;"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control" style="border-left: none; border-radius: 0 12px 12px 0; background: #e9ecef;" value="{{ $correo }}" disabled>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label" style="font-weight: 700; color: var(--ninja-purple);">Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: white; border-right: none; border-radius: 12px 0 0 12px;"><i class="bi bi-key-fill text-muted"></i></span>
                        <input type="password" name="password" id="password" class="form-control" style="border-left: none; border-radius: 0 12px 12px 0;" placeholder="Mínimo 4 caracteres" required autofocus>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password_confirmation" class="form-label" style="font-weight: 700; color: var(--ninja-purple);">Confirmar Nueva Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: white; border-right: none; border-radius: 12px 0 0 12px;"><i class="bi bi-key text-muted"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" style="border-left: none; border-radius: 0 12px 12px 0;" placeholder="Repita la contraseña" required>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn-ninja py-3 shadow-lg">
                        <i class="bi bi-check-circle me-2"></i> RESTABLECER CONTRASEÑA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
