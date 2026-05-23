@extends('diseños.staff')
@section('title', 'Promociones - Ninja Park')
@section('title_header', 'Gestión de Promociones')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- FORMULARIO NUEVA PROMOCIÓN -->
        <div class="col-md-4">
            <div class="card-modern">
                <h5 class="font-title mb-4">Nueva Promoción</h5>
                <form action="{{ route('admin.config.promociones.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Título</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Ej: Especial Día del Niño" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Precio Especial ($)</label>
                        <input type="number" step="0.01" name="precio_especial" class="form-control" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Descripción (Bot / Web)</label>
                        <textarea name="descripcion_detallada" class="form-control" rows="3" placeholder="Detalles para el cliente..."></textarea>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="esta_activa" id="activoPromoSwitch" checked>
                        <label class="form-check-label fw-bold" for="activoPromoSwitch">Activar Promoción</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="background-color: var(--primary); border: none;">
                        Crear Promoción
                    </button>
                </form>
            </div>
        </div>

        <!-- LISTADO DE PROMOCIONES -->
        <div class="col-md-8">
            <div class="card-modern">
                <h5 class="font-title mb-4">Promociones Activas y Programadas</h5>
                
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Precio</th>
                                <th>Vigencia</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promociones as $promo)
                            <tr>
                                <td class="fw-bold">{{ $promo->titulo }}</td>
                                <td>${{ number_format($promo->precio_especial, 2) }}</td>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($promo->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($promo->fecha_fin)->format('d/m/Y') }}</small></td>
                                <td>
                                    @if($promo->esta_activa)
                                        <span class="badge-pastel badge-pastel-success">Activa</span>
                                    @else
                                        <span class="badge-pastel badge-pastel-error">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <form action="{{ route('admin.config.promociones.toggle', $promo->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $promo->esta_activa ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $promo->esta_activa ? 'Desactivar' : 'Activar' }}">
                                                <i class="bi {{ $promo->esta_activa ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                            </button>
                                        </form>

                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarPromo{{ $promo->id }}" title="Editar Promoción">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('admin.config.promociones.destroy', $promo->id) }}" method="POST" class="d-inline form-delete-promo">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar Promoción">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modal Editar Promoción -->
                                    <div class="modal fade" id="modalEditarPromo{{ $promo->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-title font-title">Editar Promoción</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.config.promociones.update', $promo->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted small fw-bold">Título</label>
                                                            <input type="text" name="titulo" class="form-control" value="{{ $promo->titulo }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted small fw-bold">Precio Especial ($)</label>
                                                            <input type="number" step="0.01" name="precio_especial" class="form-control" value="{{ $promo->precio_especial }}" required>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Fecha Inicio</label>
                                                                <input type="date" name="fecha_inicio" class="form-control" value="{{ $promo->fecha_inicio }}" required>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label text-muted small fw-bold">Fecha Fin</label>
                                                                <input type="date" name="fecha_fin" class="form-control" value="{{ $promo->fecha_fin }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted small fw-bold">Descripción (Bot / Web)</label>
                                                            <textarea name="descripcion_detallada" class="form-control" rows="3">{{ $promo->descripcion_detallada }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary" style="background-color: var(--primary); border: none;">Guardar Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay promociones registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete-promo');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar Promoción?',
                    text: 'Esta acción eliminará permanentemente la promoción del sistema.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: { title: 'font-title' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
