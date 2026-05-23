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

    <!-- Local Fonts & Styles -->
    <link rel="stylesheet" href="{{ asset('fonts/outfit/outfit.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/css/flatpickr.min.css') }}">

    <style>
        :root {
            --ninja-pink: #E6007E;
            --ninja-purple: #8B5CF6;
            --ninja-cyan: #00AEF0;
            --ninja-neon: #C1FF00;
            --ninja-dark: #2d3748;
            /* Lighter dark for text */
            --ninja-white: #FFFFFF;

            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.6);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
            /* Softer shadow */
        }

        body {
            font-family: 'Outfit', sans-serif;
            /* Bright, vibrant, light animated background */
            background: linear-gradient(-45deg, #f8f9fa, #e2e8f0, #edf2f7, #f1f5f9);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            color: var(--ninja-dark);
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Floating shapes in background - softer colors for light theme */
        .blob {
            position: fixed;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.3;
            /* Less opaque for light mode */
            animation: float 10s infinite ease-in-out alternate;
        }

        .blob-1 {
            top: -10%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: var(--ninja-pink);
        }

        .blob-2 {
            bottom: -10%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: var(--ninja-cyan);
            animation-delay: -5s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) scale(1);
            }

            100% {
                transform: translateY(30px) scale(1.1);
            }
        }

        /* Navbar */
        .navbar-ninja {
            position: absolute;
            width: 100%;
            top: -100px;
            /* Fuerza la barra entera hacia arriba para que toque el techo */
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: center;
            pointer-events: none;
            /* Permite hacer clic a través de los espacios vacíos */
        }

        .navbar-brand {
            pointer-events: auto;
            /* Reactiva clicks en el logo */
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block;
        }

        .navbar-brand img {
            height: 360px;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.2));
        }

        .navbar-brand:hover img {
            transform: scale(1.05) translateY(-5px);
        }

        /* main container spacing - reduced padding to crush space below logo */
        .main-container {
            padding-top: 180px;
            /* Pulling the form upwards severely to eliminate gap */
            padding-bottom: 60px;
            z-index: 1;
            position: relative;
        }

        /* Glassmorphism Card Utilities */
        
        /* ---------------- STAFF LAYOUT (Paneles Internos) ---------------- */
        body.staff-layout .navbar-ninja {
            position: relative;
            top: 0;
            padding: 1.5rem 0 1rem 0;
            margin-bottom: 0;
            pointer-events: auto;
        }

        body.staff-layout .navbar-ninja .container {
            justify-content: flex-start !important;
        }

        body.staff-layout .navbar-brand img {
            height: auto;
            max-width: 240px; /* Tamaño orgánico armónico con col-lg-3 */
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.15));
            transition: none;
        }

        body.staff-layout .navbar-brand:hover img {
            transform: none; /* Anula el efecto de rebote en los paneles para más rigurosidad */
        }

        body.staff-layout .main-container {
            padding-top: 1rem; /* Elimina la franja gigante de 180px */
            padding-bottom: 60px;
        }

        /* Responsive Fixes Panel Interno */
        @media (max-width: 991px) {
            body.staff-layout .navbar-ninja .container {
                justify-content: center !important; /* Centrado en móviles para mantener simetría */
            }
            body.staff-layout .navbar-brand img {
                max-width: 200px;
            }
        }
        /* --------------------------------------------------------------- */

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--glass-shadow);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Buttons */
        .btn-ninja {
            background: linear-gradient(135deg, var(--ninja-pink), var(--ninja-purple));
            color: var(--ninja-white);
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 15px rgba(230, 0, 126, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-ninja::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: all 0.5s ease;
        }

        .btn-ninja:hover {
            background: linear-gradient(135deg, var(--ninja-purple), var(--ninja-pink));
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(230, 0, 126, 0.5);
            color: white;
        }

        .btn-ninja:hover::before {
            left: 100%;
        }

        .btn-ninja-outline {
            background: transparent;
            color: var(--ninja-pink);
            border: 2px solid var(--ninja-pink);
            border-radius: 12px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-ninja-outline:hover {
            background: var(--ninja-pink);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(230, 0, 126, 0.2);
        }

        /* Forms & Inputs */
        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            color: #333;
        }

        .form-control:focus,
        .form-select:focus {
            background: #ffffff;
            border-color: var(--ninja-cyan);
            box-shadow: 0 0 0 4px rgba(0, 174, 240, 0.15);
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 800;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .section-title {
            color: var(--ninja-dark);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            position: relative;
            display: inline-block;
            margin-bottom: 30px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 40px;
            height: 4px;
            background: var(--ninja-neon);
            border-radius: 2px;
        }

        /* Child Item styling */
        .child-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-left: 5px solid var(--ninja-cyan);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .child-card:hover {
            background: #ffffff;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* Simple Footer */
        footer {
            color: #718096;
            /* Darker gray for light background */
            position: relative;
            z-index: 1;
        }

        /* Flatpickr Ninja Theme Overrides */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(15px);
            border: 2px solid var(--ninja-pink) !important;
            box-shadow: 0 15px 35px rgba(230, 0, 126, 0.2) !important;
            border-radius: 20px !important;
        }
        .flatpickr-day.selected, .flatpickr-day.selected:hover {
            background: var(--ninja-pink) !important;
            border-color: var(--ninja-pink) !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(230, 0, 126, 0.3) !important;
        }
        .flatpickr-day.today {
            border-color: var(--ninja-cyan) !important;
            color: var(--ninja-cyan) !important;
        }
        .flatpickr-months .flatpickr-month {
            background: linear-gradient(135deg, var(--ninja-pink), var(--ninja-purple)) !important;
            color: #ffffff !important; /* Volver a blanco para mejor contraste */
            fill: #ffffff !important;
            border-radius: 18px 18px 0 0 !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            background: transparent !important;
            color: #ffffff !important; 
            font-weight: 700 !important;
        }
        /* Opciones del menú desplegable de meses en negro */
        .flatpickr-monthDropdown-month {
            background: #ffffff !important;
            color: #000000 !important;
        }
        .flatpickr-weekday {
            color: var(--ninja-purple) !important;
            font-weight: 800 !important;
        }
        .flatpickr-prev-month svg, .flatpickr-next-month svg {
            fill: #ffffff !important;
        }
        .flatpickr-day:hover {
            background: rgba(0, 174, 240, 0.1) !important;
            border-color: var(--ninja-cyan) !important;
        }
        .flatpickr-calendar.arrowTop:before, .flatpickr-calendar.arrowTop:after {
            border-bottom-color: var(--ninja-pink) !important;
        }

        /* Ninja Flash Effect */
        @keyframes ninja-flash-animation {
            0% { filter: brightness(1); transform: scale(1); }
            50% { filter: brightness(3); transform: scale(1.1); }
            100% { filter: brightness(1); transform: scale(1); }
        }
        .ninja-flash {
            animation: ninja-flash-animation 0.5s ease;
        }

        /* --- Ninja Custom Modal Styling --- */
        #ninjaExitModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.85); /* Deep dark glass */
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #ninjaExitModal.show {
            display: flex;
            opacity: 1;
        }

        .ninja-modal-card {
            background: rgba(255, 255, 255, 0.95);
            width: 90%;
            max-width: 500px;
            border-radius: 32px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 4px solid var(--ninja-pink);
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        #ninjaExitModal.show .ninja-modal-card {
            transform: scale(1);
        }

        .ninja-modal-icon {
            font-size: 5rem;
            color: var(--ninja-pink);
            margin-bottom: 20px;
            display: block;
            animation: pulseWarning 2s infinite;
        }

        @keyframes pulseWarning {
            0% { transform: scale(1); filter: drop-shadow(0 0 0px var(--ninja-pink)); }
            50% { transform: scale(1.1); filter: drop-shadow(0 0 15px var(--ninja-pink)); }
            100% { transform: scale(1); filter: drop-shadow(0 0 0px var(--ninja-pink)); }
        }

        .ninja-modal-title {
            font-weight: 900;
            text-transform: uppercase;
            font-size: 2rem;
            margin-bottom: 15px;
            color: var(--ninja-dark);
            letter-spacing: -1px;
        }

        .ninja-modal-text {
            color: #4b5563;
            font-size: 1.15rem;
            margin-bottom: 30px;
            line-height: 1.4;
        }

        .ninja-modal-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-modal-stay {
            background: #f3f4f6;
            color: #1f2937;
            border: none;
            border-radius: 16px;
            padding: 15px;
            font-weight: 800;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }

        .btn-modal-exit {
            background: linear-gradient(135deg, var(--ninja-pink), var(--ninja-purple));
            color: white;
            border: none;
            border-radius: 16px;
            padding: 15px;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(230, 0, 126, 0.3);
        }

        .btn-modal-stay:hover { background: #e5e7eb; transform: translateY(-2px); }
        .btn-modal-exit:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(230, 0, 126, 0.4); }
    </style>
</head>

@php
    $isStaffRoute = Request::is('staff-ninja/*') || Request::is('staff-ninja');
    $isAuthRoute = Request::is('staff-ninja') || Request::is('staff-ninja/recuperar-password') || Request::is('staff-ninja/restablecer-password');
@endphp
<body class="{{ ($isStaffRoute && !$isAuthRoute) ? 'staff-layout' : '' }}">
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

    <main class="container main-container">
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

    <footer class="text-center py-4 mt-auto">
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