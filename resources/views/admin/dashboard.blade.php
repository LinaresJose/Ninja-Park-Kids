@extends('diseños.staff')

@section('title', 'Admin Dashboard | Ninja Park')
@section('title_header', 'Panel Principal')

@section('content')
<div class="row g-4 mb-3">
    <div class="col-md-6 col-xl-4">
        <div class="card-modern d-flex align-items-center">
            <div class="me-4 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(109, 40, 217, 0.1); color: var(--primary);">
                <i class="bi bi-pen-fill" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <p class="text-muted small fw-bold mb-1 font-title">FIRMADOS HOY</p>
                <h2 class="fw-black mb-0 font-title" style="color: var(--primary);">{{ $firmasHoy }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card-modern d-flex align-items-center">
            <div class="me-4 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(109, 40, 217, 0.1); color: var(--primary);">
                <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <p class="text-muted small fw-bold mb-1 font-title">CLIENTES BASE</p>
                <h2 class="fw-black mb-0 font-title" style="color: var(--primary);">{{ $totalClientes }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <div class="card-modern d-flex align-items-center">
            <div class="me-4 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: var(--badge-success-bg); color: var(--badge-success-text);">
                <i class="bi bi-server" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <p class="text-muted small fw-bold mb-1 font-title">ESTADO DEL SISTEMA</p>
                <h5 class="fw-bold mb-0 text-success font-title">OPTIMIZADO</h5>
            </div>
        </div>
    </div>
</div>

<!-- Análisis de Rendimiento del Parque -->
<div class="row g-4 mb-4">
    <!-- Afluencia -->
    <div class="col-xl-8">
        <div class="card-modern h-100" style="position: relative;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-title mb-0">Afluencia de los Últimos 7 Días</h5>
                <div>
                    <button class="btn btn-sm btn-light border rounded-pill fw-bold" style="font-size: 0.8rem; color: #475569;" onclick="window.toggleSemanaAnterior()">
                        <i class="bi bi-clock-history me-1"></i> Comparar Sem. Ant.
                    </button>
                    <span class="badge border bg-light text-muted px-3 py-2 rounded-pill ms-2"><i class="bi bi-graph-up me-1"></i> TENDENCIA</span>
                </div>
            </div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="chartAfluencia"></canvas>
            </div>
        </div>
    </div>
    <!-- Demografía -->
    <div class="col-xl-4">
        <div class="card-modern h-100" style="position: relative;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-title mb-0">Perfil Demográfico</h5>
            </div>
            <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                <canvas id="chartDemografia"></canvas>
            </div>
        </div>
    </div>
    <!-- Horas Pico -->
    <div class="col-xl-12">
        <div class="card-modern mb-0" style="position: relative;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-title mb-0">Horas de Mayor Tráfico</h5>
                <span class="badge border bg-light text-muted px-3 py-2 rounded-pill"><i class="bi bi-clock me-1"></i> HISTORIAL</span>
            </div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="chartHoras"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración global de Chart.js
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6B7280';
    
    // Configuración común de tooltips
    const commonTooltips = {
        backgroundColor: '#1F2937',
        titleFont: { family: "'Inter', sans-serif", size: 13, weight: 'bold' },
        bodyFont: { family: "'Inter', sans-serif", size: 12 },
        padding: 12,
        cornerRadius: 8,
        displayColors: true
    };

    let afluenciaChart, horasChart, demoChart;

    const initCharts = () => {
        // Afluencia
        const ctxAfluencia = document.getElementById('chartAfluencia').getContext('2d');
        afluenciaChart = new Chart(ctxAfluencia, {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                    tooltip: commonTooltips
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' }, border: { dash: [4, 4] } },
                    x: { grid: { display: false } }
                },
                elements: { line: { tension: 0.4 } }
            }
        });

        // Toggle Semana Anterior
        window.toggleSemanaAnterior = () => {
            if (afluenciaChart.data.datasets.length > 1) {
                const isHidden = afluenciaChart.getDatasetMeta(1).hidden;
                afluenciaChart.getDatasetMeta(1).hidden = isHidden === null ? true : !isHidden;
                afluenciaChart.update();
            }
        };

        // Demografía
        const ctxDemo = document.getElementById('chartDemografia').getContext('2d');
        demoChart = new Chart(ctxDemo, {
            type: 'doughnut',
            data: { labels: [], datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, padding: 15, font: {family: "'Inter', sans-serif"} } },
                    tooltip: commonTooltips
                },
                cutout: '70%'
            }
        });

        // Horas Pico
        const ctxHoras = document.getElementById('chartHoras').getContext('2d');
        horasChart = new Chart(ctxHoras, {
            type: 'bar',
            data: { labels: [], datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: commonTooltips
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    };

    const fetchEstadisticas = async () => {
        try {
            const res = await fetch('{{ route("admin.estadisticas") }}');
            if(!res.ok) throw new Error('Error en API');
            const data = await res.json();
            
            // Actualizar Afluencia
            const cAfl = document.getElementById('chartAfluencia').getContext('2d');
            let gradient = cAfl.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(109, 40, 217, 0.4)');
            gradient.addColorStop(1, 'rgba(109, 40, 217, 0.0)');

            afluenciaChart.data = {
                labels: data.afluencia.labels,
                datasets: [
                    {
                        label: 'Semana Actual',
                        data: data.afluencia.data,
                        borderColor: '#6D28D9', // Púrpura sólido
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#6D28D9',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Semana Anterior',
                        data: data.afluencia.data_ant,
                        borderColor: '#94A3B8', // Gris azulado suave
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#94A3B8',
                        pointRadius: 3,
                        hidden: true
                    }
                ]
            };
            afluenciaChart.update('none'); // Update sin animación pesada en polling

            // Actualizar Horas Pico
            horasChart.data = {
                labels: data.horas.labels,
                datasets: [{
                    label: 'Registros',
                    data: data.horas.data,
                    backgroundColor: '#38BDF8', // Cian/Azul suave
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            };
            horasChart.update('none');

            // Actualizar Demografía
            demoChart.data = {
                labels: data.demo.labels,
                datasets: [{
                    data: data.demo.data,
                    backgroundColor: ['#6D28D9', '#93C5FD', '#86EFAC', '#C4B5FD'], // Púrpura, Azul Pastel, Verde Pastel, Violeta Claro
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            };
            demoChart.update('none');

        } catch (e) {
            console.error('No se pudo actualizar gráficas', e);
        }
    };

    initCharts();
    fetchEstadisticas();
    
    // Polling silencioso cada 60 seg
    setInterval(fetchEstadisticas, 60000);
});
</script>
