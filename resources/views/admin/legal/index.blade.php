@extends('diseños.staff')
@section('title', 'Gestión Legal - Ninja Park')
@section('title_header', 'Gestión Legal y Acuerdos')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- FORMULARIO NUEVA VERSIÓN -->
        <div class="col-md-5">
            <div class="card-modern">
                <h5 class="font-title mb-4">Redactar Nueva Versión</h5>
                <form action="{{ route('admin.legal.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre de la Versión</label>
                        <input type="text" name="version" class="form-control" value="{{ $defaultVersionName }}" placeholder="Ej: v2.0 - Enero 2026" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Contenido del Acuerdo (HTML/Texto)</label>
                        <textarea name="contenido" class="form-control" rows="10" placeholder="Escribe el contenido legal aquí..." required></textarea>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="activo" id="activoSwitch" checked>
                        <label class="form-check-label fw-bold" for="activoSwitch">Activar esta versión inmediatamente</label>
                        <div class="form-text text-warning"><i class="bi bi-exclamation-triangle"></i> Esto desactivará cualquier otra versión anterior.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="background-color: var(--primary); border: none;">
                        Guardar Versión
                    </button>
                </form>
            </div>
        </div>

        <!-- LISTADO DE VERSIONES -->
        <div class="col-md-7">
            <div class="card-modern">
                <h5 class="font-title mb-4">Historial de Versiones</h5>
                
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Versión</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($versiones as $ver)
                            <tr>
                                <td class="fw-bold">{{ $ver->version }}</td>
                                <td>
                                    @if($ver->activo)
                                        <span class="badge-pastel badge-pastel-success"><i class="bi bi-check-circle-fill me-1"></i> ACTIVA</span>
                                    @else
                                        <span class="badge-pastel" style="background: #F1F5F9; color: #64748B;">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$ver->activo)
                                        <form action="{{ route('admin.legal.activar', $ver->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                Hacer Activa
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay versiones registradas.</td>
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
