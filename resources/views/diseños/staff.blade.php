<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ninja Park Control')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Offline Fonts -->
    <link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/montserrat/montserrat.css') }}">

    <!-- Vendor Styles -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/css/flatpickr.min.css') }}">

    <style>
        :root {
            --primary: #6D28D9;
            --primary-hover: #5B21B6;
            --bg-body: #F8FAFC;
            --text-main: #1F2937;
            --text-muted: #6B7280;
            --text-inactive-hover: #4B5563;

            /* Badges Pastel */
            --badge-success-bg: #DCFCE7;
            --badge-success-text: #166534;
            --badge-warning-bg: #FEF3C7;
            --badge-warning-text: #92400E;
            --badge-error-bg: #FEE2E2;
            --badge-error-text: #991B1B;

            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            margin: 0;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        /* SIDEBAR GLOBAL */
        .staff-sidebar {
            width: var(--sidebar-width);
            background-color: #FFFFFF;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        .staff-sidebar-logo {
            padding: 0rem 1rem 0.5rem 1rem;
            text-align: center;
            margin-top: -45px;
            margin-bottom: -35px;
        }

        .staff-sidebar-logo img {
            max-width: 100%;
            height: auto;
            max-height: 170px;
            object-fit: contain;
        }

        /* NAVEGACIÓN DE PANELES */
        .staff-nav {
            padding: 0 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-item-ninja {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-item-ninja i {
            font-size: 1.25rem;
            margin-right: 12px;
        }

        .nav-item-ninja:hover {
            color: var(--text-inactive-hover);
            background: #F1F5F9;
        }

        .nav-item-ninja.active {
            background-color: var(--primary);
            color: #FFFFFF;
        }

        .nav-item-ninja.active i {
            color: #E2E8F0;
        }

        /* LOGOUT BUTTON */
        .sidebar-footer {
            padding: 1.5rem 1rem;
            border-top: 1px solid #F1F5F9;
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border-radius: 8px;
            color: #EF4444;
            background: #FEF2F2;
            border: 1px solid #FEE2E2;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #FEE2E2;
            color: #DC2626;
        }

        /* MAIN WRAPPER */
        .staff-main {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
        }

        /* TRANSLUCENT TOP HEADER */
        .staff-header {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* CARDS MODERNAS */
        .card-modern {
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            transition: box-shadow 0.2s ease;
            margin-bottom: 1.5rem;
        }

        /* TABLAS REFACTORIZADAS */
        .table-modern {
            width: 100%;
            margin-bottom: 0;
        }

        .table-modern th {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 1rem 1.5rem;
            border-bottom: 2px solid #F1F5F9;
        }

        .table-modern td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            font-size: 0.95rem;
        }

        /* BADGES PASTEL */
        .badge-pastel-success {
            background: var(--badge-success-bg);
            color: var(--badge-success-text);
        }

        .badge-pastel-warning {
            background: var(--badge-warning-bg);
            color: var(--badge-warning-text);
        }

        .badge-pastel-error {
            background: var(--badge-error-bg);
            color: var(--badge-error-text);
        }

        .badge-pastel {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* RESPONSIVE */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-main);
        }

        @media (max-width: 991px) {
            .staff-sidebar {
                transform: translateX(-100%);
            }

            .staff-sidebar.show {
                transform: translateX(0);
            }

            .staff-main {
                margin-left: 0;
                width: 100%;
            }

            .mobile-toggle {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- MENÚ LATERAL (SIDEBAR) -->
    <aside class="staff-sidebar" id="mainSidebar">
        <div class="staff-sidebar-logo">
            <img src="{{ asset('img/logo.png') }}" alt="Ninja Park Kids">
        </div>

        <div class="px-3 mb-4 text-center">
            <div class="small text-muted font-title text-uppercase">Sesión como</div>
            <div class="fw-bold" style="color: var(--primary);">{{ Auth::user()->nombre }}</div>
            <span class="badge"
                style="background:#F1F5F9; color: var(--text-muted);">{{ Auth::user()->rol->nombre_rol }}</span>
        </div>

        <nav class="staff-nav">
            @if(Auth::user()->esGerente() || Auth::user()->esAdmin())
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item-ninja {{ Request::is('staff-ninja/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Panel Principal
                </a>
                <a href="{{ route('admin.reportes') }}"
                    class="nav-item-ninja {{ Request::is('staff-ninja/reportes*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Reportes y Descargas
                </a>
                @if(Auth::user()->esGerente())
                    <div class="px-2 mt-4 mb-2 text-muted small text-uppercase font-title fw-bold" style="font-size: 0.75rem;">Configuración del Parque</div>
                    
                    <a href="{{ route('admin.legal.index') }}" class="nav-item-ninja {{ Request::is('staff-ninja/legal*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i> Gestión Legal
                    </a>
                    <a href="{{ route('admin.config.tarifas') }}" class="nav-item-ninja {{ Request::is('staff-ninja/tarifas*') || Request::is('staff-ninja/horarios*') ? 'active' : '' }}">
                        <i class="bi bi-clock-fill"></i> Tarifas y Horarios
                    </a>
                    <a href="{{ route('admin.config.promociones') }}" class="nav-item-ninja {{ Request::is('staff-ninja/promociones*') ? 'active' : '' }}">
                        <i class="bi bi-tags-fill"></i> Promociones
                    </a>

                    <div class="px-2 mt-4 mb-2 text-muted small text-uppercase font-title fw-bold" style="font-size: 0.75rem;">Administración</div>
                    <a href="{{ route('admin.users') }}"
                        class="nav-item-ninja {{ Request::is('staff-ninja/usuarios') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Gestión Usuarios
                    </a>
                @endif
            @endif

            @if(Auth::user()->tieneAccesoOperador())
                <a href="{{ route('operador.dashboard') }}"
                    class="nav-item-ninja {{ Request::is('staff-ninja/operador') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan"></i> Control de Acceso
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="button" class="btn-logout" onclick="confirmLogout()">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <main class="staff-main">
        <!-- TOP HEADER -->
        <header class="staff-header">
            <div class="d-flex align-items-center">
                <button class="mobile-toggle me-3">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="font-title mb-0 d-none d-md-block">@yield('title_header', 'Panel de Control')</h4>
            </div>

            <div class="d-flex flex-row align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="position-relative d-flex h-3 w-3">
                        <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-circle opacity-75"
                            style="background:var(--badge-success-text); width:8px; height:8px; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                        <span class="position-relative inline-flex rounded-circle"
                            style="background:var(--badge-success-text); width:8px; height:8px;"></span>
                    </span>
                    <span class="badge-pastel badge-pastel-success">Sistema Online</span>
                </div>
            </div>
        </header>

        <!-- MAIN PAGE CONTENT -->
        <div class="p-4" style="flex-grow: 1;">
            @yield('content')
        </div>
    </main>

    <!-- Overlay para móvil -->
    <div id="sidebarOverlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1035;">
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script defer src="{{ asset('vendor/alpinejs/alpine.min.js') }}"></script>
    <script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>

    <style>
        @keyframes ping {

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
    </style>

    <script>
        // Toggle Sidebar Móvil
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleMobileMenu() {
            sidebar.classList.toggle('show');
            overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
        }

        document.querySelector('.mobile-toggle').addEventListener('click', toggleMobileMenu);
        overlay.addEventListener('click', toggleMobileMenu);

        // SweetAlert2 para Logout con advertencia del escáner
        function confirmLogout() {
            Swal.fire({
                title: '¿Cerrar Sesión?',
                text: '¿Estás seguro de que deseas cerrar sesión? Al salir, el escáner dejará de funcionar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    title: 'font-title'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }
    </script>
    
    @include('partials.malochy-widget', ['modo' => 'staff'])
@stack('scripts')
</body>

</html>