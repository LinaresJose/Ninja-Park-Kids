@extends('diseños.staff')
@section('title', 'Tarifas y Horarios - Ninja Park')
@section('title_header', 'Configuración de Tarifas y Horarios')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- SECCIÓN TARIFAS -->
        <div class="col-md-7">
            <div class="card-modern mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-title m-0">Gestión de Tarifas</h5>
                    <button class="btn btn-sm btn-primary" style="background-color: var(--primary); border: none;" data-bs-toggle="modal" data-bs-target="#modalNuevaTarifa">
                        <i class="bi bi-plus-lg"></i> Nueva Tarifa
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Duración (Min)</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tarifas as $tarifa)
                            <tr>
                                <td class="fw-bold">{{ $tarifa->nombre_tarifa }}</td>
                                <td>{{ $tarifa->duracion_minutos ? $tarifa->duracion_minutos . ' min' : 'Ilimitado' }}</td>
                                <td>${{ number_format($tarifa->precio, 2) }}</td>
                                <td>
                                    @if($tarifa->esta_activa)
                                        <span class="badge-pastel badge-pastel-success">Activa</span>
                                    @else
                                        <span class="badge-pastel badge-pastel-error">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.config.tarifas.toggle', $tarifa->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $tarifa->esta_activa ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $tarifa->esta_activa ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay tarifas configuradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECCIÓN HORARIOS -->
        <div class="col-md-5">
            <div class="card-modern">
                <h5 class="font-title mb-4">Horario Operativo</h5>
                <form action="{{ route('admin.config.horarios.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    @php $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']; @endphp
                    
                    @foreach($horarios as $horario)
                    <div class="d-flex align-items-center justify-content-between mb-3 gap-2">
                        <div class="fw-bold" style="width: 100px; min-width: 100px;">{{ $dias[$horario->dia_semana] }}</div>
                        <div class="flex-grow-1" style="min-width: 110px;">
                            <input type="time" name="horarios[{{ $horario->id }}][hora_apertura]" class="form-control form-control-sm" value="{{ $horario->hora_apertura }}">
                        </div>
                        <div class="flex-grow-1" style="min-width: 110px;">
                            <input type="time" name="horarios[{{ $horario->id }}][hora_cierre]" class="form-control form-control-sm" value="{{ $horario->hora_cierre }}">
                        </div>
                        <div class="text-end" style="width: 90px; min-width: 90px;">
                            <div class="form-check form-switch d-inline-block text-start">
                                <input class="form-check-input" type="checkbox" name="horarios[{{ $horario->id }}][esta_cerrado]" value="1" {{ $horario->esta_cerrado ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted">Cerrado</label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <button type="submit" class="btn btn-primary w-100 mt-3" style="background-color: var(--primary); border: none;">
                        Guardar Horarios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Tarifa -->
<div class="modal fade" id="modalNuevaTarifa" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0">
        <h5 class="modal-title font-title">Nueva Tarifa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.config.tarifas.store') }}" method="POST">
          @csrf
          <div class="modal-body">
              <div class="mb-3">
                  <label class="form-label">Nombre</label>
                  <input type="text" name="nombre_tarifa" class="form-control" required placeholder="Ej: 1 Hora">
              </div>
              <div class="mb-3">
                  <label class="form-label">Duración (Minutos)</label>
                  <input type="number" name="duracion_minutos" class="form-control" placeholder="Dejar vacío para tiempo ilimitado">
              </div>
              <div class="mb-3">
                  <label class="form-label">Precio ($)</label>
                  <input type="number" step="0.01" name="precio" class="form-control" required>
              </div>
              <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="esta_activa" checked>
                  <label class="form-check-label">Activa</label>
              </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary" style="background-color: var(--primary); border: none;">Guardar Tarifa</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
