@extends('diseños.staff')

@section('title', 'Gestión de Usuarios | Ninja Park')
@section('title_header', 'Gestión de Personal')

@section('content')
<div class="card-modern">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-title mb-0">Directorio de Usuarios</h4>
        <button class="btn btn-sm text-white rounded-3 fw-bold shadow-sm" style="background: var(--primary); padding: 10px 20px;" data-bs-toggle="modal" data-bs-target="#newUserModal">
            <i class="bi bi-person-plus-fill me-2"></i> NUEVO USUARIO
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center rounded-3 bg-opacity-10 border-0 text-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center rounded-3 bg-opacity-10 border-0 text-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger rounded-3 bg-opacity-10 border-0 text-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO / TELÉFONO</th>
                    <th>ROL DE ACCESO</th>
                    <th class="text-center">ESTADO</th>
                    <th class="text-end">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $user)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ strtoupper($user->nombre) }} {{ strtoupper($user->apellido) }}</div>
                        <small class="text-muted">C.I: {{ $user->cedula }}</small>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $user->correo }}</div>
                    </td>
                    <td>
                        <span class="badge border bg-light text-dark py-2 px-3 rounded-pill" style="font-weight: 500;">
                            {{ strtoupper($user->rol->nombre_rol) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($user->estado)
                            <span class="badge-pastel badge-pastel-success">ACTIVO</span>
                        @else
                            <span class="badge-pastel badge-pastel-error">INACTIVO</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-light border me-1"><i class="bi bi-pencil-square text-muted"></i></button>
                        
                        <form action="{{ route('staff.users.destroy', $user->id) }}" method="POST" class="d-inline form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border"><i class="bi bi-trash3 text-danger"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Nuevo Usuario -->
<div class="modal fade" id="newUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title font-title"><i class="bi bi-person-plus text-primary me-2"></i> Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('staff.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold font-title">NOMBRE</label>
                            <input type="text" name="nombre" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold font-title">APELLIDO</label>
                            <input type="text" name="apellido" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold font-title">CÉDULA</label>
                            <input type="text" name="cedula" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold font-title">ROL ASIGNADO</label>
                            <select name="rol_id" class="form-select form-select-lg bg-light border-0" required>
                                <option value="">Seleccione...</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ strtoupper($rol->nombre_rol) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold font-title">CORREO CORPORATIVO</label>
                            <input type="email" name="correo" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold font-title">CONTRASEÑA TEMPORAL</label>
                            <input type="password" name="password" class="form-control form-control-lg bg-light border-0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-3 px-4 shadow-sm" style="background: var(--primary);">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar Usuario?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        title: 'font-title'
                    }
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
