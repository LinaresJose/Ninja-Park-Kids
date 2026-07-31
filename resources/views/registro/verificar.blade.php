@extends('diseños.app')

@section('title', 'Bienvenido | Ninja Park')

@section('content')
<div class="row justify-content-center align-items-center">
    <div class="col-md-6 col-lg-5">
        <div class="glass-card p-5 text-center">
            
            <div class="mb-4">
                <i class="bi bi-person-badge" style="font-size: 3.5rem; color: var(--ninja-pink); filter: drop-shadow(0 0 10px rgba(230,0,126,0.3));"></i>
            </div>

            <h2 class="fw-black mb-2" style="font-weight: 900; letter-spacing: -1px; color: var(--ninja-dark);">
                Acreditación
            </h2>
            <p class="text-muted mb-5" style="font-size: 1.1rem;">
                Ingresa tu Cédula o Pasaporte para comenzar el registro
            </p>

            <form action="{{ route('registro.consultar') }}" method="POST">
                @csrf
                <div class="mb-4 position-relative">
                    <input autoFocus type="text" name="cedula" id="cedulaInput" class="form-control form-control-lg text-center" 
                           placeholder="Ej: 12345678" required 
                           inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                           maxlength="8"
                           style="font-size: 1.25rem; font-weight: 600; padding: 18px;">
                </div>
                
                <button type="submit" class="btn-ninja w-100 py-3" style="font-size: 1.1rem;">
                    <i class="bi bi-arrow-right-circle me-2"></i> CONTINUAR
                </button>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Pequeño script para hacer focus animado suave
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Focus Smooth
        const input = document.getElementById('cedulaInput');
        setTimeout(() => {
            input.focus();
        }, 300);

        // 2. Auto-renovar token CSRF cada 10 minutos para evitar error 419
        setInterval(() => {
            fetch('/csrf-token-refresh', { method: 'GET', credentials: 'same-origin' })
                .then(r => r.json())
                .then(data => {
                    document.querySelectorAll('input[name="_token"]').forEach(el => el.value = data.token);
                }).catch(() => {});
        }, 10 * 60 * 1000);
    });

</script>
@endpush
@endsection