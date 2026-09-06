<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ninja Park Control')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/faviconV2.png') }}">

    <!-- Offline Fonts -->
    <link rel="stylesheet" href="{{ asset('fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/montserrat/montserrat.css') }}">

    <!-- Vendor Styles -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/css/flatpickr.min.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>

    <!-- MENÚ LATERAL (SIDEBAR) -->
    <aside class="staff-sidebar" id="mainSidebar">
        <div class="staff-sidebar-logo d-flex justify-content-center align-items-center">
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
                    <span class="position-relative d-inline-flex align-items-center justify-content-center" style="width: 10px; height: 10px;">
                        <span class="position-absolute rounded-circle opacity-75"
                            style="background: var(--badge-success-text); width: 100%; height: 100%; top: 0; left: 0; animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                        <span class="position-relative rounded-circle"
                            style="background: var(--badge-success-text); width: 8px; height: 8px;"></span>
                    </span>
                    <span class="badge-pastel badge-pastel-success">Sistema Online</span>
                </div>
            </div>
        </header>

        <!-- MAIN PAGE CONTENT -->
        <div class="p-4" style="flex-grow: 1;">
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 12px; border-left: 5px solid #EF4444; background: rgba(255,255,255,0.95); color: #7F1D1D;">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: #EF4444;"></i> <strong>Existen errores en el formulario:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
    
    @include('partials.maloshy-widget', ['modo' => 'staff'])
@stack('scripts')
</body>

</html>