<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ninja Park Kids')</title>
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#E6007E">
    <link rel="apple-touch-icon" href="{{ asset('img/icon-192.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/faviconV2.png') }}">

    <!-- Local Fonts & Styles -->
    <link rel="stylesheet" href="{{ asset('fonts/outfit/outfit.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/css/flatpickr.min.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $isStaffRoute = Request::is('staff-ninja/*') || Request::is('staff-ninja');
    $isAuthRoute = Request::is('staff-ninja') || Request::is('staff-ninja/recuperar-password') || Request::is('staff-ninja/restablecer-password');
@endphp
<body class="d-flex flex-column min-vh-100 {{ ($isStaffRoute && !$isAuthRoute) ? 'staff-layout' : '' }}">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <!-- Header Navbar -->
    <nav class="navbar navbar-ninja">
        <div class="container justify-content-center">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Ninja Park Logo">
            </a>
        </div>
    </nav>

    <main class="container main-container flex-grow-1 d-flex flex-column justify-content-center">
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 12px; border-left: 5px solid red; background: rgba(255,255,255,0.9);">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Existen errores en el formulario:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="text-center py-4 mt-auto w-100 position-relative" style="z-index: 10;">
        <p class="mb-0 fw-semibold">&copy; {{ date('Y') }} Ninja Park Kids</p>
        <small>Sistema de Registro y Generación de Pases</small>
    </footer>

    <!-- Custom Navigation Modal (Secret until triggered) -->
    <div id="ninjaExitModal">
        <div class="ninja-modal-card">
            <i class="bi bi-exclamation-triangle ninja-modal-icon"></i>
            <h3 class="ninja-modal-title">¡Atención!</h3>
            <p class="ninja-modal-text">
                Si sales ahora, se perderán todos los datos actuales del proceso. <br>
                <strong>¿Estás seguro de que quieres volver al inicio?</strong>
            </p>
            <div class="ninja-modal-buttons">
                <button type="button" class="btn-modal-stay" onclick="closeNinjaModal()">NO, QUEDARME</button>
                <button type="button" class="btn-modal-exit" onclick="confirmNinjaExit()">SÍ, SALIR</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/flatpickr/js/flatpickr.js') }}"></script>
    <script src="{{ asset('vendor/flatpickr/js/es.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script defer src="{{ asset('vendor/alpinejs/alpine.min.js') }}"></script>

    <script>
        // --- Registro de Service Worker para PWA ---
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(reg => console.log('Ninja SW registrado', reg))
                    .catch(err => console.log('Error SW', err));
            });
        }
    </script>

    <script>
        /**
         * GLOBAL NINJA LOGO MANAGER v2 (Simplified Triple-Click)
         * - 3 Clics Rápidos = Acceso Directo Staff.
         * - 400ms de Búfer para detectar la secuencia antes de mostrar el modal.
         * - Reinicio de contador tras 1s de inactividad.
         */
        (function() {
            const logo = document.querySelector('.navbar-brand');
            const logoImg = logo ? logo.querySelector('img') : null;
            if (!logo) return;

            let clickCount = 0;
            let lastClickTime = 0;
            let modalTimer = null;
            
            const MODAL_DELAY = 400; // ms para esperar el siguiente clic antes de actuar
            const SEQUENCE_RESET = 1000; // ms para borrar clics viejos
            const SENSITIVE_ROUTES = ['/nuevo-registro', '/registro/firma', '/pase'];

            function isProcessActive() {
                const path = window.location.pathname;
                return SENSITIVE_ROUTES.some(route => path.startsWith(route));
            }

            logo.addEventListener('click', (e) => {
                // Desactivar el trigger del logo dentro del panel de staff
                if (window.location.pathname.startsWith('/staff-ninja')) {
                    if (window.location.pathname.startsWith('/staff-ninja/login')) {
                        // permitimos refresh en login
                    } else {
                        return; // neutralizamos clics en el panel interno
                    }
                }

                e.preventDefault(); 
                const currentTime = Date.now();

                // 1. Limpiar clics accidentales esparcidos en el tiempo
                if (currentTime - lastClickTime > SEQUENCE_RESET) {
                    clickCount = 0;
                }
                
                clickCount++;
                lastClickTime = currentTime;

                // 2. DETECCIÓN DE TRIPLE CLIC (LLAVE MAESTRA)
                if (clickCount === 3) {
                    // PRIORIDAD TOTAL: Cancelamos cualquier modal pendiente
                    clearTimeout(modalTimer);
                    
                    if ("vibrate" in navigator) navigator.vibrate([50, 50, 50]);
                    if (logoImg) logoImg.classList.add('ninja-flash');
                    
                    // Acción inmediata: Redirigir al Staff Panel
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 300);
                    return;
                }

                // 3. RETARDO DE CONTROL (MODAL O REFRESH)
                // Esperamos 400ms después de cada clic para ver si viene otro
                clearTimeout(modalTimer);
                modalTimer = setTimeout(() => {
                    if (clickCount < 3) {
                        if (isProcessActive()) {
                            // En rutas de proceso, mostramos el modal personalizado
                            openNinjaModal();
                        } else {
                            // En la home o rutas seguras, refrescamos con normalidad
                            window.location.href = "{{ url('/') }}";
                        }
                    }
                    clickCount = 0; // Reiniciamos el contador tras ejecutar la acción
                }, MODAL_DELAY);
            });

            // Funciones globales del modal
            window.openNinjaModal = function() {
                const modal = document.getElementById('ninjaExitModal');
                if (modal) modal.classList.add('show');
            };

            window.closeNinjaModal = function() {
                const modal = document.getElementById('ninjaExitModal');
                if (modal) modal.classList.remove('show');
                clickCount = 0; 
            };

            window.confirmNinjaExit = function() {
                window.location.href = "{{ url('/') }}";
            };
        })();
    </script>

    
    @if(Request::is('/'))
        @include('partials.malochy-widget', ['modo' => 'public'])
    @endif
@stack('scripts')
</body>

</html>